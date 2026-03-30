<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Мои объявления') }}
        </h2>
    </x-slot>

        <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('listings.create') }}" class=" rounded font-semibold py-2 px-4 bg-white ">
                    Создать объявление
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Мои объявления</h3>
                    
                    @if($listings->count() > 0)
                        <div class="space-y-4">
                            @foreach($listings as $listing)
                                <div class="border rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <a href="{{ route('listings.show', $listing) }}" class="hover:text-blue-600">
                                                <h4 class="text-xl font-semibold">{{ $listing->title }}</h4>
                                            </a>
                                            <p class="text-gray-600 mt-1">{{ Str::limit($listing->description, 100) }}</p>
                                            <div class="mt-2 flex gap-4 text-sm text-gray-500">
                                                <span>Категория: {{ $listing->category->name }}</span>
                                                <span>
                                                    @if($listing->price)
                                                        @if($listing->price_type == 'fixed')
                                                            {{ $listing->price }} ₽
                                                        @elseif($listing->price_type == 'hour')
                                                            {{ $listing->price }} ₽/час
                                                        @else
                                                            Договорная
                                                        @endif
                                                    @else
                                                        Цена не указана
                                                    @endif
                                                </span>
                                                <span>Просмотров: {{ $listing->views_count }}</span>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <span class="px-2 py-1 text-xs rounded {{ $listing->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $listing->is_active ? 'Активно' : 'Неактивно' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500"></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
