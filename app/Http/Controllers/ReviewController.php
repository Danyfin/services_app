<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function create(Order $order)
    {
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Только заказчик может оставить отзыв');
        }
        
        if ($order->status !== 'completed') {
            abort(403, 'Отзыв можно оставить только после завершения заказа');
        }
        
        if ($order->review) {
            abort(403, 'Отзыв уже оставлен');
        }
        
        return view('reviews.create', compact('order'));
    }
    
    public function store(Request $request, Order $order)
    {
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Только заказчик может оставить отзыв');
        }
        
        if ($order->status !== 'completed') {
            abort(403, 'Отзыв можно оставить только после завершения заказа');
        }
        
        if ($order->review) {
            abort(403, 'Отзыв уже оставлен');
        }
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);
        
        $review = Review::create([
            'order_id' => $order->id,
            'reviewer_id' => Auth::id(),
            'executor_id' => $order->executor_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        $order->executor->updateRating();
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Спасибо за отзыв!');
    }
}