@extends('layouts.app')

@section('title', 'Поиск услуг')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        @if($searchQuery)
            Результаты поиска: "{{ $searchQuery }}"
        @elseif(isset($selectedCategoryId) && $selectedCategoryId)
            @php
                $cat = App\Models\Category::find($selectedCategoryId);
            @endphp
            @if($cat)
                Услуги в категории: {{ $cat->name }}
            @else
                Все услуги
            @endif
        @else
            Все услуги
        @endif
    </h1>
    
    <!-- Форма поиска и фильтров -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('listings.search') }}" class="space-y-4">
            
            <div>
                <input type="text" 
                       name="search" 
                       value="{{ $searchQuery }}"
                       placeholder="Поиск услуг..." 
                       class="w-full rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <select name="category_id" class="rounded-md border-gray-300">
                    <option value="">Все категории</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ ($selectedCategoryId ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @foreach($cat->children as $child)
                            <option value="{{ $child->id }}" {{ ($selectedCategoryId ?? '') == $child->id ? 'selected' : '' }}>
                                &nbsp;&nbsp;&nbsp;└ {{ $child->name }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
                
                <input type="number" 
                       name="price_min" 
                       value="{{ request('price_min') }}"
                       placeholder="Цена от"
                       class="rounded-md border-gray-300">
                
                <input type="number" 
                       name="price_max" 
                       value="{{ request('price_max') }}"
                       placeholder="Цена до"
                       class="rounded-md border-gray-300">
                
                <select name="sort" class="rounded-md border-gray-300">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Сначала новые</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Цена: по возрастанию</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Цена: по убыванию</option>
                </select>
            </div>
            
            <div class="flex justify-end gap-3">
                <a href="{{ route('listings.search') }}" class="px-4 py-2 border rounded-md hover:bg-gray-50">
                    Сбросить
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Найти
                </button>
            </div>
        </form>
    </div>
    
    @if($listings->count() > 0)
        <div class="space-y-4">
            @foreach($listings as $listing)
                <div class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition cursor-pointer"
                     onclick="window.location='{{ route('listings.show', $listing) }}'">
                    <div class="flex gap-4">
                        <!-- Фото -->
                        @if($listing->images->first())
                            <img src="{{ asset('storage/' . $listing->images->first()->image_path) }}" 
                                 class="w-24 h-24 object-cover rounded-lg">
                        @else
                            <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-sm">
                                Нет фото
                            </div>
                        @endif
                        
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 hover:text-indigo-600">
                                {{ $listing->title }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Категория: {{ $listing->category->name ?? 'Без категории' }}
                            </p>
                            <p class="text-gray-600 text-sm mt-1 line-clamp-2">
                                {{ Str::limit($listing->description, 100) }}
                            </p>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-indigo-600 font-bold">
                                    @if($listing->price_type == 'fixed')
                                        {{ number_format($listing->price, 0, ',', ' ') }} ₽
                                    @elseif($listing->price_type == 'hour')
                                        {{ number_format($listing->price, 0, ',', ' ') }} ₽/час
                                    @else
                                        Договорная
                                    @endif
                                </span>
                                <div class="flex items-center gap-3 text-sm text-gray-500">
                                    <span>{{ $listing->views_count }}</span>
                                    <span>
                                        <a href="{{ route('profile.user', $listing->user) }}" class="text-indigo-600 hover:underline">
                                            {{ $listing->user->name }}
                                        </a>
                                    </span>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $listings->appends(request()->query())->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <p class="text-gray-500 text-lg mb-2">Ничего не найдено</p>
            <p class="text-gray-400">Попробуйте изменить параметры поиска</p>
            <a href="{{ route('listings.search') }}" class="inline-block mt-4 text-indigo-600 hover:underline">
                Сбросить фильтры
            </a>
        </div>
    @endif
</div>
@endsection