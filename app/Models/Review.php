<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
        use HasFactory;

    protected $fillable = [
        'rating',
        'comment',
        'user_id',
        'executor_id',
        'order_id',
    ];

    // Связи
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function executor()
    {
        return $this->belongsTo(User::class, 'executor_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
