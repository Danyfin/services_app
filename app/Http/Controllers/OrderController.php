<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $ordersAsCustomer = Order::where('customer_id', Auth::id())
            ->with(['listing', 'executor'])
            ->latest()
            ->get();

        $ordersAsExecutor = Order::where('executor_id', Auth::id())
            ->with(['listing', 'customer'])
            ->latest()
            ->get();

        return view('orders.index', compact('ordersAsCustomer', 'ordersAsExecutor'));
    }

    public function show(Order $order)
    {
        if (!$order->canBeManagedBy(Auth::user())) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'description' => 'required|string|max:2000',
        ]);

        $listing = Listing::findOrFail($request->listing_id);

        if ($listing->user_id == Auth::id()) {
            return back()->with('error', 'Нельзя заказать услугу у самого себя');
        }

        $existingOrder = Order::where('listing_id', $listing->id)
            ->where('customer_id', Auth::id())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->first();

        if ($existingOrder) {
            return back()->with('error', 'У вас уже есть активный заказ на эту услугу');
        }

        $order = Order::create([
            'listing_id' => $listing->id,
            'customer_id' => Auth::id(),
            'executor_id' => $listing->user_id,
            'description' => $request->description,
            'price' => $listing->price,
            'status' => Order::STATUS_PENDING,
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Заказ создан! Ожидайте подтверждения от исполнителя.');
    }

    public function accept(Order $order)
    {
        if (!$order->canBeAcceptedBy(Auth::user())) {
            abort(403);
        }

        $order->accept();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Заказ принят.');
    }

    public function start(Order $order)
    {
        if (!$order->canBeStartedBy(Auth::user())) {
            abort(403);
        }

        $order->start();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Работа над заказом начата.');
    }

    public function complete(Order $order)
    {
        if (!$order->canBeCompletedBy(Auth::user())) {
            abort(403);
        }

        $order->complete();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Заказ завершён.');
    }

    public function cancel(Request $request, Order $order)
    {
        if (!$order->canBeCancelledBy(Auth::user())) {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (Auth::id() == $order->customer_id) {
            $order->cancelByCustomer($request->reason);
            $message = 'Заказ отменён. Причина сохранена.';
        } else {
            $order->cancelByExecutor($request->reason);
            $message = 'Заказ отменён. Причина сохранена.';
        }

        return redirect()->route('orders.show', $order)
            ->with('success', $message);
    }

    public function storeReview(Request $request, Order $order)
    {
        if (!$order->canBeReviewedBy(Auth::user())) {
            abort(403, 'Нельзя оставить отзыв.');
        }
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);
        
        $review = new \App\Models\Review();
        $review->order_id = $order->id;
        $review->user_id = Auth::id();
        $review->executor_id = $order->executor_id;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->save();
        
        $order->executor->updateRating();
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Спасибо за отзыв!');
    }
}