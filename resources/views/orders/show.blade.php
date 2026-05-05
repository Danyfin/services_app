@extends('layouts.app')

@section('title', 'Заказ #' . $order->id)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        
        <div class="flex justify-between items-start mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Заказ #{{ $order->id }}
            </h1>
            <span class="inline-block px-3 py-1 text-sm rounded-full
                @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                @elseif($order->status == 'accepted') bg-blue-100 text-blue-800
                @elseif($order->status == 'in_progress') bg-purple-100 text-purple-800
                @elseif($order->status == 'completed') bg-green-100 text-green-800
                @else bg-red-100 text-red-800
                @endif">
                {{ \App\Models\Order::STATUSES[$order->status] }}
            </span>
        </div>
        
        <div class="mb-6">
            <h3 class="font-semibold text-gray-700 mb-2">Услуга</h3>
            <a href="{{ route('listings.show', $order->listing) }}" class="text-indigo-600 hover:underline">
                {{ $order->listing->title }}
            </a>
        </div>
        
        <div class="mb-6">
            <h3 class="font-semibold text-gray-700 mb-2">Описание задачи</h3>
            <p class="text-gray-600 whitespace-pre-line">{{ $order->description }}</p>
        </div>
        
        <div class="mb-6">
            <h3 class="font-semibold text-gray-700 mb-2">Стоимость</h3>
            <p class="text-xl font-bold text-indigo-600">
                {{ number_format($order->price, 0, ',', ' ') }} ₽
            </p>
        </div>
        
        <div class="mb-6 grid grid-cols-2 gap-4">
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Заказчик</h3>
                <p class="text-gray-600">{{ $order->customer->name }}</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Исполнитель</h3>
                <p class="text-gray-600">{{ $order->executor->name }}</p>
            </div>
        </div>
        
        @if($order->review)
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-2">Отзыв заказчика</h3>
                <div class="flex items-center gap-2 mb-2">
                    <div class="flex text-yellow-500">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $order->review->rating)
                                ★
                            @else
                                ☆
                            @endif
                        @endfor
                    </div>
                </div>
                <p class="text-gray-600">{{ $order->review->comment ?? 'Без комментария' }}</p>
            </div>
        @endif
        
        <div class="border-t pt-6 flex flex-wrap gap-3">
            @if($order->canBeAcceptedBy(Auth::user()))
                <form action="{{ route('orders.accept', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        Принять заказ
                    </button>
                </form>
            @endif
            
            @if($order->canBeStartedBy(Auth::user()))
                <form action="{{ route('orders.start', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Начать выполнение
                    </button>
                </form>
            @endif
            
            @if($order->canBeCompletedBy(Auth::user()))
                <form action="{{ route('orders.complete', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                        Завершить заказ
                    </button>
                </form>
            @endif
            
            @if($order->canBeCancelledBy(Auth::user()))
                <button onclick="showCancelModal()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    Отменить заказ
                </button>
            @endif
        </div>
        
        @if($order->canBeReviewedBy(Auth::user()))
            <div class="border-t pt-6 mt-6">
                <h3 class="font-semibold text-gray-800 mb-4">Оставить отзыв</h3>
                <form action="{{ route('reviews.store', $order) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ваша оценка</label>
                        <div class="flex gap-2 rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" data-rating="{{ $i }}" class="rating-star text-3xl text-gray-300 hover:text-yellow-500 focus:outline-none transition">
                                    ☆
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" required>
                        @error('rating')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Ваш отзыв</label>
                        <textarea name="comment" id="comment" rows="4" 
                                  class="w-full rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                                  placeholder="Расскажите о качестве работы, соблюдении сроков, профессионализме...">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700">
                        Отправить отзыв
                    </button>
                </form>
            </div>
        @elseif($order->isCompleted() && !$order->review && Auth::id() != $order->customer_id)
            <div class="border-t pt-6 mt-6 text-center text-gray-400 text-sm">
                Только заказчик может оставить отзыв
            </div>
        @endif
        
        @if($order->isCancelled())
            <div class="mt-6 bg-red-50 p-4 rounded-lg">
                <h3 class="font-semibold text-red-800 mb-2">Причина отмены</h3>
                @if($order->customer_cancellation_reason)
                    <p class="text-red-700 text-sm">Заказчик: {{ $order->customer_cancellation_reason }}</p>
                @endif
                @if($order->executor_cancellation_reason)
                    <p class="text-red-700 text-sm">Исполнитель: {{ $order->executor_cancellation_reason }}</p>
                @endif
            </div>
        @endif
    </div>
</div>

<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-bold mb-4">Отмена заказа</h3>
        <form id="cancelForm" method="POST">
            @csrf
            @method('DELETE')
            <textarea name="reason" rows="3" class="w-full rounded-md border-gray-300 mb-4" 
                      placeholder="Укажите причину отмены..." required></textarea>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="hideCancelModal()" class="px-4 py-2 border rounded-md hover:bg-gray-50">
                    Отмена
                </button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    Подтвердить отмену
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const stars = document.querySelectorAll('.rating-star');
    const ratingInput = document.getElementById('ratingInput');
    let currentRating = 0;
    
    function updateStars(rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.innerHTML = '★';
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-500');
            } else {
                star.innerHTML = '☆';
                star.classList.remove('text-yellow-500');
                star.classList.add('text-gray-300');
            }
        });
        ratingInput.value = rating;
    }
    
    stars.forEach(star => {
        star.addEventListener('click', () => {
            const rating = parseInt(star.dataset.rating);
            currentRating = rating;
            updateStars(rating);
        });
        
        star.addEventListener('mouseenter', () => {
            const rating = parseInt(star.dataset.rating);
            stars.forEach((s, idx) => {
                if (idx < rating) {
                    s.innerHTML = '★';
                    s.classList.remove('text-gray-300');
                    s.classList.add('text-yellow-500');
                } else {
                    s.innerHTML = '☆';
                    s.classList.remove('text-yellow-500');
                    s.classList.add('text-gray-300');
                }
            });
        });
    });
    
    const container = document.querySelector('.rating-stars');
    if (container) {
        container.addEventListener('mouseleave', () => {
            updateStars(currentRating);
        });
    }
    
    function showCancelModal() {
        const modal = document.getElementById('cancelModal');
        const form = document.getElementById('cancelForm');
        form.action = '{{ route("orders.cancel", $order) }}';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function hideCancelModal() {
        const modal = document.getElementById('cancelModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>
@endsection