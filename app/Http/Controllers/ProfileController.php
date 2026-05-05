<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Review;
use App\Models\Order;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        $activeListings = $user->listings()
            ->with(['category', 'images'])
            ->where('is_active', true)
            ->latest()
            ->paginate(10);
        
        $inactiveListings = $user->listings()
            ->with(['category', 'images'])
            ->where('is_active', false)
            ->latest()
            ->paginate(10);
        
        $listings = $user->listings()->latest()->paginate(6);
        
        $reviews = Review::where('executor_id', $user->id)
            ->with(['reviewer', 'order.listing'])
            ->latest()
            ->paginate(5);
        
        $reviewsCount = Review::where('executor_id', $user->id)->count();
        $ordersCount = Order::where('executor_id', $user->id)->count();

        $favoriteListings = $user->favorites()
        ->with(['category', 'images'])
        ->latest()
        ->paginate(10);
        
        return view('profile.show', compact(
            'user', 
            'activeListings', 
            'inactiveListings', 
            'favoriteListings',
            'reviews', 
            'reviewsCount', 
            'ordersCount'
        ));
    }
    
    public function userProfile(User $user)
    {
        $listings = $user->listings()
            ->with(['category', 'images'])
            ->where('is_active', true)
            ->latest()
            ->paginate(10);
        
        $reviews = Review::where('executor_id', $user->id)
            ->with(['reviewer', 'order.listing'])
            ->latest()
            ->paginate(10);
        
        $reviewsCount = Review::where('executor_id', $user->id)->count();
        $ordersCount = Order::where('executor_id', $user->id)->count();
        $isOwnProfile = Auth::check() && Auth::id() == $user->id;
        
        $messages = collect();
        if (Auth::check() && !$isOwnProfile) {
            $messages = Message::where(function($q) use ($user) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $user->id);
            })->orWhere(function($q) use ($user) {
                $q->where('sender_id', $user->id)->where('receiver_id', Auth::id());
            })->orderBy('created_at')->get();
        }
        
        return view('profile.user', compact(
            'user', 
            'listings', 
            'reviews', 
            'reviewsCount', 
            'ordersCount', 
            'isOwnProfile',
            'messages'
        ));
    }
    
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'about' => 'nullable|string|max:2000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->about = $request->about;
        
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }
        
        $user->save();
        
        return redirect()->route('profile.show')->with('success', 'Профиль обновлён');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $user = Auth::user();
        
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();
        
        return back()->with('success', 'Аватар обновлён');
    }
}