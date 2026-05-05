<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Category;
use App\Models\Region;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ListingController extends Controller
{
    public function welcome()
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();
        
        $regions = Region::where('is_active', true)->get();
        $currentRegion = Region::find(session('current_region_id'));
        
        return view('welcome', compact('categories', 'regions', 'currentRegion'));
    }
    
    public function search(Request $request)
    {
        $query = Listing::with(['user', 'category', 'region', 'images'])
            ->where('is_active', true);
        
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }
        
        if ($request->filled('category_id')) {
            $category = Category::find($request->category_id);
            if ($category) {
                $categoryIds = $this->getAllCategoryIds($category);
                $query->whereIn('category_id', $categoryIds);
            }
        }
        
        if (session('current_region_id')) {
            $query->where('region_id', session('current_region_id'));
        }
        
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }
        
        switch ($request->get('sort', 'latest')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest();
        }
        
        $listings = $query->paginate(15)->appends($request->query());
        
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();
        
        $regions = Region::where('is_active', true)->get();
        $currentRegion = Region::find(session('current_region_id'));
        $searchQuery = $request->get('search', '');
        $selectedCategoryId = $request->get('category_id');
        
        return view('listings.search-results', compact(
            'listings', 
            'categories', 
            'regions', 
            'currentRegion', 
            'searchQuery',
            'selectedCategoryId'
        ));
    }

    private function getAllCategoryIds($category)
    {
        $ids = [$category->id];
        foreach ($category->children as $child) {
            $ids[] = $child->id;
            foreach ($child->children as $grandchild) {
                $ids[] = $grandchild->id;
            }
        }
        return $ids;
    }
    
    public function show(Listing $listing)
    {
        $listing->incrementViews();
        
        $messages = Message::where('listing_id', $listing->id)
            ->where(function($q) {
                $q->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
            })
            ->orderBy('created_at')
            ->get();
        
        $regions = Region::where('is_active', true)->get();
        $currentRegion = Region::find(session('current_region_id'));
        
        return view('listings.show', compact('listing', 'messages', 'regions', 'currentRegion'));
    }
    
    public function create()
    {
        $categories = Category::all();
        $regions = Region::where('is_active', true)->get();
        $currentRegion = Region::find(session('current_region_id'));
        
        return view('listings.create', compact('categories', 'regions', 'currentRegion'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|numeric|min:0',
            'price_type' => 'required|in:fixed,hour,negotiable',
            'category_id' => 'required|exists:categories,id',
            'region_id' => 'nullable|exists:regions,id',
            'address' => 'nullable|string|max:255',
            'order_requirements' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);
        
        $validated['user_id'] = Auth::id();
        $validated['is_active'] = true;
        $validated['views_count'] = 0;
        
        if (!$validated['region_id'] && session('current_region_id')) {
            $validated['region_id'] = session('current_region_id');
        }
        
        $listing = Listing::create($validated);
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('listings', 'public');
                $listing->images()->create([
                    'image_path' => $path,
                    'sort_order' => $index,
                ]);
            }
        }
        
        return redirect()->route('listings.show', $listing)
            ->with('success', 'Объявление создано!');
    }
    
    public function edit(Listing $listing)
    {
        if ($listing->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }
        
        $categories = Category::orderBy('name')->get();
        $regions = Region::where('is_active', true)->get();
        $currentRegion = Region::find(session('current_region_id'));
        
        return view('listings.edit', compact('listing', 'categories', 'regions', 'currentRegion'));
    }
    
    public function update(Request $request, Listing $listing)
    {
        if ($listing->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|numeric|min:0',
            'price_type' => 'required|in:fixed,hour,negotiable',
            'category_id' => 'required|exists:categories,id',
            'region_id' => 'nullable|exists:regions,id',
            'address' => 'nullable|string',
            'order_requirements' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);
        
        $listing->update($validated);
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('listings', 'public');
                $listing->images()->create([
                    'image_path' => $path,
                    'sort_order' => $listing->images()->count() + $index,
                ]);
            }
        }
        
        return redirect()->route('listings.show', $listing)
            ->with('success', 'Объявление обновлено!');
    }
    
    public function destroy(Listing $listing)
    {
        if ($listing->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }
        
        foreach ($listing->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
        
        $listing->delete();
        
        return redirect()->route('dashboard')
            ->with('success', 'Объявление удалено');
    }
    
    public function myListings()
    {
        $listings = Listing::with(['category', 'region'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
        
        $regions = Region::where('is_active', true)->get();
        $currentRegion = Region::find(session('current_region_id'));
        
        return view('listings.my-listings', compact('listings', 'regions', 'currentRegion'));
    }
}