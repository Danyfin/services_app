@extends('layouts.app')

@section('title', 'Избранное')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Избранное</h1>
    
    @if($favorites->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($favorites as $listing)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <a href="{{ route('listings.show', $listing) }}">
                        @if($listing->images->first())
                            <img src="{{ asset('storage/' . $listing->images->first()->image_path) }}" 
                                 alt="{{ $listing->title }}"
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                                Нет фото
                            </div>
                        @endif
                    </a>
                    <div class="p-4">
                        <a href="{{ route('listings.show', $listing) }}" class="hover:underline">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $listing->title }}</h3>
                        </a>
                        <p class="text-gray-600 text-sm mt-1">{{ $listing->category->name ?? 'Без категории' }}</p>
                        <div class="mt-3 flex justify-between items-center">
                            <span class="text-indigo-600 font-bold">
                                @if($listing->price_type == 'fixed')
                                    {{ number_format($listing->price, 0, ',', ' ') }} ₽
                                @elseif($listing->price_type == 'hour')
                                    {{ number_format($listing->price, 0, ',', ' ') }} ₽/час
                                @else
                                    Договорная
                                @endif
                            </span>
                            <button onclick="toggleFavorite({{ $listing->id }})" 
                                    class="text-red-500 hover:text-red-700 text-sm">
                                ♥ Удалить
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $favorites->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <div class="text-4xl mb-4">💔</div>
            <p class="text-gray-500 text-lg mb-2">У вас нет избранных объявлений</p>
            <a href="{{ route('welcome') }}" class="text-indigo-600 hover:underline">Перейти к поиску услуг</a>
        </div>
    @endif
</div>

<script>
function toggleFavorite(listingId) {
    fetch(`/favorites/${listingId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endsection