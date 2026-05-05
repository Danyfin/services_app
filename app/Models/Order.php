<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'customer_id',
        'executor_id',
        'status',
        'description',
        'price',
        'start_date',
        'end_date',
        'customer_cancellation_reason',
        'executor_cancellation_reason',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING => 'Ожидает подтверждения',
        self::STATUS_ACCEPTED => 'Принят',
        self::STATUS_IN_PROGRESS => 'В работе',
        self::STATUS_COMPLETED => 'Завершён',
        self::STATUS_CANCELLED => 'Отменён',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function executor()
    {
        return $this->belongsTo(User::class, 'executor_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function accept()
    {
        $this->update(['status' => self::STATUS_ACCEPTED]);
    }

    public function start()
    {
        $this->update(['status' => self::STATUS_IN_PROGRESS]);
    }

    public function complete()
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    public function cancelByCustomer(string $reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'customer_cancellation_reason' => $reason,
        ]);
    }

    public function cancelByExecutor(string $reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'executor_cancellation_reason' => $reason,
        ]);
    }

    public function canBeManagedBy(User $user): bool
    {
        return $user->id === $this->customer_id || $user->id === $this->executor_id || $user->isAdmin();
    }

    public function canBeAcceptedBy(User $user): bool
    {
        return $this->isPending() && $user->id === $this->executor_id;
    }

    public function canBeStartedBy(User $user): bool
    {
        return $this->isAccepted() && $user->id === $this->executor_id;
    }

    public function canBeCompletedBy(User $user): bool
    {
        return $this->isInProgress() && $user->id === $this->executor_id;
    }

    public function canBeCancelledBy(User $user): bool
    {
        if ($this->isCompleted() || $this->isCancelled()) {
            return false;
        }
        return $user->id === $this->customer_id || $user->id === $this->executor_id;
    }

    public function canBeReviewedBy(User $user): bool
    {
        return $this->isCompleted() && $user->id === $this->customer_id && !$this->review;
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where('customer_id', $user->id)
            ->orWhere('executor_id', $user->id);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
}