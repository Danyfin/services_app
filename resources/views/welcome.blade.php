@extends('layouts.app')

@section('title', 'Поиск мастеров')

@section('content')
<div class="bg-indigo-900 text-white bg-[url('/public/storage/img/background.png')] bg-cover">
    <div class="max-w-7xl px-8 mx-auto py-20 ">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">
            Нужен мастер за 10 минут?
        </h1>
        <p class="text-xl md:text-2xl text-indigo-100 mb-10">
            Проверенные специалисты рядом с вами. От сантехника до репетитора.
        </p>
        
        <form method="GET" action="{{ route('listings.search') }}" class="max-w mx-auto">
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="text" 
                    name="search" 
                    placeholder="Поиск услуг..." 
                    value="{{ request('search') }}"
                    class="flex-1 px-6 py-4 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-00 text-white font-semibold px-8 py-4 rounded-lg transition">
                    Найти
                </button>
            </div>
        </form>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">
        Выберите категорию услуг
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $chunks = $categories->chunk(ceil($categories->count() / 4));
        @endphp
        
        @foreach($chunks as $chunk)
            <div class="space-y-4">
                @foreach($chunk as $category)
                    <div class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition">
                        <a href="{{ route('categories.show', $category->slug) }}" class="block">
                            <h3 class="font-semibold text-gray-800 text-lg mb-2 hover:text-indigo-600">
                                {{ $category->name }}
                            </h3>
                        </a>
                        
                        @if($category->children->count() > 0)
                            <div class="space-y-1">
                                @foreach($category->children->take(5) as $child)
                                    <a href="{{ route('categories.show', $child->slug) }}" 
                                       class="text-sm text-gray-500 hover:text-indigo-600 block">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                                @if($category->children->count() > 5)
                                    <a href="{{ route('categories.show', $category->slug) }}" 
                                       class="text-xs text-indigo-500 hover:underline block mt-1">
                                        + еще {{ $category->children->count() - 5 }}
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="text-sm text-gray-400">Нет подкатегорий</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
@endsection