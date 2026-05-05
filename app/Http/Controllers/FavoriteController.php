<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Auth::user()->favorites()
            ->with(['user', 'category', 'images'])
            ->latest()
            ->paginate(12);
        
        return view('favorites.index', compact('favorites'));
    }
    
    public function toggle(Listing $listing)
    {
        $user = Auth::user();
        
        if ($user->hasFavorited($listing->id)) {
            $user->favorites()->detach($listing->id);
            $isFavorited = false;
        } else {
            $user->favorites()->attach($listing->id);
            $isFavorited = true;
        }
        
        if (request()->ajax()) {
            return response()->json(['success' => true, 'isFavorited' => $isFavorited]);
        }
        
        return back();
    }
}