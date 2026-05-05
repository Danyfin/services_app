<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Listing;
use App\Models\User;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $key = 'send-message:' . Auth::id();
        
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'success' => false, 
                'error' => 'Слишком много сообщений. Подождите немного.'
            ], 429);
        }
        
        RateLimiter::hit($key, 60);
        
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);
        
        if ($request->receiver_id == Auth::id()) {
            return response()->json(['success' => false, 'error' => 'Нельзя писать самому себе'], 400);
        }
        
        $cleanMessage = strip_tags($request->message);
        $cleanMessage = trim($cleanMessage);
        
        if (empty($cleanMessage)) {
            return response()->json(['success' => false, 'error' => 'Сообщение не может быть пустым'], 400);
        }
        
        $listing = Listing::find($request->listing_id);
        if (!$listing || ($listing->user_id != Auth::id() && $request->receiver_id != $listing->user_id)) {
            return response()->json(['success' => false, 'error' => 'Нет доступа'], 403);
        }
        
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'listing_id' => $request->listing_id,
            'message' => $cleanMessage,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'time' => $message->created_at->format('H:i'),
            ]
        ]);
    }
    
    public function poll(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'last_id' => 'nullable|integer',
        ]);
        
        $listing = Listing::find($request->listing_id);
        if (!$listing || ($listing->user_id != Auth::id() && !$this->isUserInDialog($request->listing_id))) {
            return response()->json(['error' => 'Нет доступа'], 403);
        }
        
        $query = Message::where('listing_id', $request->listing_id)
            ->where(function($q) {
                $q->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
            });
        
        if ($request->last_id) {
            $query->where('id', '>', $request->last_id);
        }
        
        $messages = $query->orderBy('created_at')->get();
        
        foreach ($messages as $message) {
            if ($message->receiver_id == Auth::id() && !$message->is_read) {
                $message->update(['is_read' => true]);
            }
        }
        
        return response()->json([
            'messages' => $messages->map(function($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_id' => $msg->sender_id,
                    'time' => $msg->created_at->format('H:i'),
                ];
            })
        ]);
    }
    
    private function isUserInDialog($listingId)
    {
        return Message::where('listing_id', $listingId)
            ->where(function($q) {
                $q->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
            })
            ->exists();
    }
    
    public function index()
    {
        $sentTo = Message::where('sender_id', Auth::id())->pluck('receiver_id');
        $receivedFrom = Message::where('receiver_id', Auth::id())->pluck('sender_id');
        
        $userIds = $sentTo->merge($receivedFrom)->unique();
        
        $dialogs = User::whereIn('id', $userIds)->get();
        
        foreach ($dialogs as $dialog) {
            $lastMessage = Message::where(function($q) use ($dialog) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $dialog->id);
            })->orWhere(function($q) use ($dialog) {
                $q->where('sender_id', $dialog->id)->where('receiver_id', Auth::id());
            })->latest()->first();
            
            $dialog->last_message = $lastMessage;
            $dialog->unread_count = Message::where('sender_id', $dialog->id)
                ->where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->count();
                
            if ($lastMessage) {
                $dialog->listing = Listing::find($lastMessage->listing_id);
            }
        }
        
        $dialogs = $dialogs->sortByDesc(function($dialog) {
            return $dialog->last_message?->created_at;
        });
        
        $regions = Region::where('is_active', true)->get();
        $currentRegion = Region::find(session('current_region_id'));
        
        return view('messages.index', compact('dialogs', 'regions', 'currentRegion'));
    }
    
    public function dialog($userId)
    {
        $otherUser = User::findOrFail($userId);
        
        $hasDialog = Message::where(function($q) use ($userId) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $userId);
        })->orWhere(function($q) use ($userId) {
            $q->where('sender_id', $userId)->where('receiver_id', Auth::id());
        })->exists();
        
        if (!$hasDialog && Auth::id() != $userId) {
            abort(403, 'Нет доступа к этому диалогу');
        }
        
        $messages = Message::where(function($q) use ($userId) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $userId);
        })->orWhere(function($q) use ($userId) {
            $q->where('sender_id', $userId)->where('receiver_id', Auth::id());
        })->orderBy('created_at')->get();
        
        Message::where('sender_id', $userId)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        $sentTo = Message::where('sender_id', Auth::id())->pluck('receiver_id');
        $receivedFrom = Message::where('receiver_id', Auth::id())->pluck('sender_id');
        $userIds = $sentTo->merge($receivedFrom)->unique();
        
        $dialogs = User::whereIn('id', $userIds)->get();
        
        foreach ($dialogs as $dialog) {
            $lastMessage = Message::where(function($q) use ($dialog) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $dialog->id);
            })->orWhere(function($q) use ($dialog) {
                $q->where('sender_id', $dialog->id)->where('receiver_id', Auth::id());
            })->latest()->first();
            
            $dialog->last_message = $lastMessage;
            $dialog->unread_count = Message::where('sender_id', $dialog->id)
                ->where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->count();
                
            if ($lastMessage) {
                $dialog->listing = Listing::find($lastMessage->listing_id);
            }
        }
        
        $dialogs = $dialogs->sortByDesc(function($dialog) {
            return $dialog->last_message?->created_at;
        });
        
        $regions = Region::where('is_active', true)->get();
        $currentRegion = Region::find(session('current_region_id'));
        
        return view('messages.index', compact('dialogs', 'messages', 'otherUser', 'regions', 'currentRegion'));
    }
}