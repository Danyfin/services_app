<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RegionController extends Controller
{
    public function switch($slug)
    {
        $region = Region::where('slug', $slug)->firstOrFail();
        Session::put('current_region_id', $region->id);
        Session::put('current_region_name', $region->name);
        
        return back();
    }
}