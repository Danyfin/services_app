@extends('layouts.app')

@section('title', 'Мои заказы')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Заказы (как заказчик)</h2>
        
        @if($ordersAsCustomer->count() > 0)
            <div class="space-y-4">
                @foreach($ordersAsCustomer as $order)
                    <div class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <a href="{{ route('orders.show', $order) }}" class="hover:underline">
                                    <h3 class="font-semibold text-lg">{{ $order->listing->title }}</h3>
                                </a>
                                <p class="text-gray-600 text-sm mt-1">Исполнитель: {{ $order->executor->name }}</p>
                                <p class="text-gray-500 text-sm">{{ Str::limit($order->description, 100) }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-3 py-1 text-sm rounded-full
                                    @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'accepted') bg-blue-100 text-blue-800
                                    @elseif($order->status == 'in_progress') bg-purple-100 text-purple-800
                                    @elseif($order->status == 'completed') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ \App\Models\Order::STATUSES[$order->status] }}
                                </span>
                                <p class="text-sm text-gray-500 mt-1">{{ $order->created_at->format('d.m.Y') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-500">
                <p>У вас нет заказов как у заказчика</p>
                <a href="{{ route('welcome') }}" class="inline-block mt-2 text-indigo-600 hover:underline">Найти услуги</a>
            </div>
        @endif
    </div>
    
    <div>
        <h2 class="text-xl font-bold text-gray-800 mb-4">Заказы (как исполнитель)</h2>
        
        @if($ordersAsExecutor->count() > 0)
            <div class="space-y-4">
                @foreach($ordersAsExecutor as $order)
                    <div class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <a href="{{ route('orders.show', $order) }}" class="hover:underline">
                                    <h3 class="font-semibold text-lg">{{ $order->listing->title }}</h3>
                                </a>
                                <p class="text-gray-600 text-sm mt-1">Заказчик: {{ $order->customer->name }}</p>
                                <p class="text-gray-500 text-sm">{{ Str::limit($order->description, 100) }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-3 py-1 text-sm rounded-full
                                    @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'accepted') bg-blue-100 text-blue-800
                                    @elseif($order->status == 'in_progress') bg-purple-100 text-purple-800
                                    @elseif($order->status == 'completed') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ \App\Models\Order::STATUSES[$order->status] }}
                                </span>
                                <p class="text-sm text-gray-500 mt-1">{{ $order->created_at->format('d.m.Y') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-500">
                <p>У вас нет заказов как у исполнителя</p>
            </div>
        @endif
    </div>
</div>
@endsection