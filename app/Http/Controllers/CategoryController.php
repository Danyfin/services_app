<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use App\Models\Region;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->with('children')
            ->firstOrFail();
        
        $categoryIds = $this->getAllCategoryIds($category);
        
        $query = Listing::with(['user', 'category', 'region', 'images'])
            ->whereIn('category_id', $categoryIds)
            ->where('is_active', true);
        
        if (session('current_region_id')) {
            $query->where('region_id', session('current_region_id'));
        }
        
        $listings = $query->latest()->paginate(20);
        
        $searchQuery = $category->name;
        
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();
        
        $regions = Region::where('is_active', true)->get();
        $currentRegion = Region::find(session('current_region_id'));
        
        return view('listings.search-results', compact(
            'listings', 
            'categories', 
            'regions', 
            'currentRegion', 
            'searchQuery',
            'category'
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
}