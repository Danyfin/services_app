@extends('layouts.app')

@section('title', 'Оставить отзыв')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Оставить отзыв</h1>
        
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <p class="text-gray-600 font-medium">{{ $order->listing->title }}</p>
            <p class="text-gray-600 text-sm mt-1">Исполнитель: {{ $order->executor->name }}</p>
        </div>
        
        <form action="{{ route('reviews.store', $order) }}" method="POST">
            @csrf
            
            <!-- Оценка -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ваша оценка *</label>
                <div class="flex gap-4 items-center">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="flex flex-col items-center gap-1 cursor-pointer hover:scale-110 transition">
                            <input type="radio" name="rating" value="{{ $i }}" class="w-4 h-4" required>
                            <span class="text-2xl text-yellow-400">★</span>
                            <span class="text-xs text-gray-500">{{ $i }}</span>
                        </label>
                    @endfor
                </div>
                @error('rating')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Комментарий -->
            <div class="mb-6">
                <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Ваш отзыв</label>
                <textarea name="comment" id="comment" rows="5" 
                          class="w-full rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                          placeholder="Расскажите о качестве работы, соблюдении сроков, профессионализме...">{{ old('comment') }}</textarea>
                @error('comment')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Кнопки -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('orders.show', $order) }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                    Отмена
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                    Отправить отзыв
                </button>
            </div>
        </form>
    </div>
</div>
@endsection