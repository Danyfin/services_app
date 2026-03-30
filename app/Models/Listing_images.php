<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing_images extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'image_path',
        'sort_order',
    ];

    // Связи
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
