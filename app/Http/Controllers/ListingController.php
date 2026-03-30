<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
            
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('listings.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'price_type' => ['required', 'in:fixed,hour,negotiable'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        Listing::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'price_type' => $request->price_type,
            'address' => $request->address,
            'is_active' => true,
        ]);

        return redirect()->route('dashboard')->with('success', 'Объявление создано!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Listing $listing)
    {
        $listing->increment('views_count');
    
        return view('listings.show', compact('listing'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            abort(403, 'У вас нет прав на редактирование этого объявления');
        }
        
        $categories = Category::where('is_active', true)->get();
        
        return view('listings.edit', compact('listing', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            abort(403, 'У вас нет прав на редактирование этого объявления');
        }
        
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'price_type' => ['required', 'in:fixed,hour,negotiable'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        
        $listing->update([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'price_type' => $request->price_type,
            'address' => $request->address,
            'is_active' => $request->has('is_active'),
        ]);
        
        return redirect()->route('listings.show', $listing)->with('success', 'Объявление обновлено!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            abort(403, 'У вас нет прав на удаление этого объявления');
        }
        
        $listing->delete();
        
        return redirect()->route('dashboard')->with('success', 'Объявление удалено!');
    }
}
