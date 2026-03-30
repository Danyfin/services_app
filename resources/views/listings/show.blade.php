<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $listing->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-4">
                        <a href="{{ url()->previous() }}" class="text-blue-600 hover:text-blue-800">
                            ← Назад
                        </a>
                    </div>

                    <h1 class="text-3xl font-bold mb-4">{{ $listing->title }}</h1>
                    
                    <div class="flex items-center gap-4 mb-6 text-sm text-gray-600">
                        <span>Автор: {{ $listing->user->name }}</span>
                        <span>Рейтинг: {{ number_format($listing->user->rating_avg, 1) }}</span>
                        <span>Просмотров: {{ $listing->views_count }}</span>
                        <span>Дата: {{ $listing->created_at->format('d.m.Y') }}</span>
                    </div>

                    <div class="flex gap-4 mb-6">
                        <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded text-sm">
                            {{ $listing->category->name }}
                        </span>
                    </div>

                    <h3 class="font-semibold text-lg">Цена:</h3>
                        <span class=text-lg font-semibold">
                            @if($listing->price)
                                @if($listing->price_type == 'fixed')
                                    {{ number_format($listing->price, 0) }} &#8381
                                @elseif($listing->price_type == 'hour')
                                    {{ number_format($listing->price, 0) }} &#8381/час
                                @else
                                    Договорная цена
                                @endif
                            @else
                                Цена не указана
                            @endif
                        </span>

                    @if($listing->address)
                        <div class="mb-6">
                            <h3 class="font-semibold text-lg mb-2">Адрес:</h3>
                            <p class="text-gray-700">{{ $listing->address }}</p>
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="font-semibold text-lg mb-2">Описание:</h3>
                        <div class="text-gray-700">
                            {{ $listing->description }}
                        </div>
                    </div>

                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-lg mb-2">Контакты</h3>
                        <p class="text-gray-700">Имя: {{ $listing->user->name }}</p>
                        @if($listing->user->phone)
                            <p class="text-gray-700">Телефон: {{ $listing->user->phone }}</p>
                        @endif
                        <p class="text-gray-700">Email: {{ $listing->user->email }}</p>
                    </div>

                    @if(Auth::id() === $listing->user_id)
                        <div class="flex gap-3 mt-6">
                            <a href="{{ route('listings.edit', $listing) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Редактировать
                            </a>
                            
                            <form action="{{ route('listings.destroy', $listing) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить это объявление?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>