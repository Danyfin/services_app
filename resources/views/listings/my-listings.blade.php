@extends('layouts.app')

@section('title', 'Мои объявления')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Мои объявления</h1>
        <a href="{{ route('listings.create') }}" 
           class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            + Добавить объявление
        </a>
    </div>
    
    @if($listings->count() > 0)
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Объявление</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Категория</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Цена</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Просмотры</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($listings as $listing)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($listing->images->first())
                                        <img src="{{ asset('storage/' . $listing->images->first()->image_path) }}" 
                                             class="w-10 h-10 rounded object-cover">
                                    @else
                                        <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center text-gray-400 text-xs">
                                            Нет
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $listing->title }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($listing->description, 50) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $listing->category->name ?? 'Без категории' }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                @if($listing->price_type == 'fixed')
                                    {{ number_format($listing->price, 0, ',', ' ') }} ₽
                                @elseif($listing->price_type == 'hour')
                                    {{ number_format($listing->price, 0, ',', ' ') }} ₽/час
                                @else
                                    Договорная
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($listing->is_active)
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Активно</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Неактивно</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $listing->views_count }}
                            </td>
                            <td class="px-6 py-4 flex gap-2">
                                <a href="{{ route('listings.show', $listing) }}" 
                                   class="text-indigo-600 hover:text-indigo-800 text-sm">
                                    Просмотр
                                </a>
                                <a href="{{ route('listings.edit', $listing) }}" 
                                   class="text-yellow-600 hover:text-yellow-800 text-sm">
                                    Ред.
                                </a>
                                <form action="{{ route('listings.destroy', $listing) }}" method="POST" 
                                      onsubmit="return confirm('Удалить объявление?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                        Удалить
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-6">
            {{ $listings->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <p class="text-gray-500 text-lg">У вас пока нет объявлений</p>
            <a href="{{ route('listings.create') }}" class="inline-block mt-4 text-indigo-600 hover:underline">
                Создать объявление
            </a>
        </div>
    @endif
</div>
@endsection