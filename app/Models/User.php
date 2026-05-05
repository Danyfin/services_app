<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'login',
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',
        'rating_avg',
        'about',
        'address',
        'region_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'rating_avg' => 'decimal:2',
    ];

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function isOnline()
    {
        return $this->updated_at && $this->updated_at->diffInMinutes(now()) < 5;
    }

    public function ordersAsCustomer()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function ordersAsExecutor()
    {
        return $this->hasMany(Order::class, 'executor_id');
    }
    
    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'executor_id');
    }

    public function favorites()
    {
        return $this->belongsToMany(Listing::class, 'favorites')->withTimestamps();
    }

    public function hasFavorited($listingId)
    {
        return $this->favorites()->where('listing_id', $listingId)->exists();
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function updateRating()
    {
        $avgRating = $this->reviewsReceived()->avg('rating') ?? 0;
        $this->update(['rating_avg' => round($avgRating, 2)]);
    }
}