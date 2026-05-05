@extends('layouts.app')

@section('title', 'Мой профиль')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl border border-gray-100 p-6 mb-8">
        <div class="flex flex-col md:flex-row gap-6">
            <div class="relative flex-shrink-0">
                <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-3xl font-bold text-gray-500">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <button onclick="openAvatarModal()" 
                        class="absolute top-0 right-0 bg-white rounded-full p-1.5 shadow-md border border-gray-200 hover:bg-gray-50 transition">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                </button>
            </div>
            
            <div class="flex-1">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex gap-5">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                        <div class="flex items-center gap-3 mt-1 flex-wrap">
                            @if($user->isOnline())
                                <span class="text-sm text-green-600 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Онлайн
                                </span>
                            @else
                                <span class="text-sm text-gray-400 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    Был(а) в сети
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <div class="text-xs text-gray-400">ДАТА РЕГИСТРАЦИИ</div>
                        <div class="text-sm font-medium text-gray-800">{{ $user->created_at->translatedFormat('d F Y, H:i') }}</div>
                        <div class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400">РЕЙТИНГ ПРОДАВЦА</div>
                        <div class="flex items-center gap-1">
                            <span class="text-lg font-bold text-gray-900">{{ number_format($user->rating_avg ?? 0, 1) }}</span>
                            <div class="flex text-yellow-500 text-sm">
                                @php $rating = $user->rating_avg ?? 0; @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($rating)) ★ @else ☆ @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400">ОТЗЫВОВ</div>
                        <a href="#reviews" class="text-sm font-medium text-indigo-600 hover:underline">{{ $reviewsCount }}</a>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400">ЗАКАЗОВ</div>
                        <div class="text-sm font-medium text-gray-800">{{ $ordersCount }}</div>
                    </div>
                </div>
                
                @if($user->about)
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <div class="text-xs text-gray-400 mb-1">О СЕБЕ</div>
                        <p class="text-sm text-gray-700">{{ $user->about }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-900">Предложения</h2>
        <div class="flex gap-2">
            <button type="button" data-filter="active" class="filter-btn px-3 py-1 text-sm rounded-lg bg-indigo-600 text-white transition">
                Активные
            </button>
            <button type="button" data-filter="inactive" class="filter-btn px-3 py-1 text-sm rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Неактивные
            </button>
            <button type="button" data-filter="favorite" class="filter-btn px-3 py-1 text-sm rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Избранное
            </button>
        </div>
        <a href="{{ route('listings.create') }}" class="text-sm text-indigo-600 hover:text-indigo-700 border border-indigo-200 px-3 py-1.5 rounded-lg">
            Создать услугу
        </a>
    </div>
    
    <div id="activeListings" class="bg-white rounded-xl border border-gray-100 overflow-hidden mb-12">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500">Услуга</th>
                    <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 w-32">Цена</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activeListings as $listing)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 cursor-pointer transition"
                        onclick="window.location='{{ route('listings.show', $listing) }}'">
                        <td class="py-3 px-4">
                            <div class="font-medium text-gray-900">{{ $listing->title }}</div>
                            <div class="text-sm text-gray-500">{{ $listing->category->name ?? 'Без категории' }}</div>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <span class="font-semibold text-gray-900">
                                @if($listing->price_type == 'fixed')
                                    {{ number_format($listing->price, 0, ',', ' ') }} ₽
                                @elseif($listing->price_type == 'hour')
                                    {{ number_format($listing->price, 0, ',', ' ') }} ₽/час
                                @else
                                    Договорная
                                @endif
                            </span>
                        </td>
                    </tr>
                @endforeach
                @if($activeListings->count() == 0)
                    <tr>
                        <td colspan="2" class="py-8 text-center text-gray-400">
                            Нет активных предложений
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        @if($activeListings->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $activeListings->links() }}
            </div>
        @endif
    </div>
    
    <div id="inactiveListings" class="bg-white rounded-xl border border-gray-100 overflow-hidden mb-12 hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500">Услуга</th>
                    <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 w-32">Цена</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inactiveListings as $listing)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 cursor-pointer transition"
                        onclick="window.location='{{ route('listings.show', $listing) }}'">
                        <td class="py-3 px-4">
                            <div class="font-medium text-gray-900">{{ $listing->title }}</div>
                            <div class="text-sm text-gray-500">{{ $listing->category->name ?? 'Без категории' }}</div>
                            <span class="text-xs text-gray-400">Неактивно</span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <span class="font-semibold text-gray-900">
                                @if($listing->price_type == 'fixed')
                                    {{ number_format($listing->price, 0, ',', ' ') }} ₽
                                @elseif($listing->price_type == 'hour')
                                    {{ number_format($listing->price, 0, ',', ' ') }} ₽/час
                                @else
                                    Договорная
                                @endif
                            </span>
                        </td>
                    </tr>
                @endforeach
                @if($inactiveListings->count() == 0)
                    <tr>
                        <td colspan="2" class="py-8 text-center text-gray-400">
                            Нет неактивных предложений
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div id="favoriteListings" class="bg-white rounded-xl border border-gray-100 overflow-hidden mb-12 hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500">Услуга</th>
                    <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 w-32">Цена</th>
                </tr>
            </thead>
            <tbody>
                @foreach($favoriteListings as $listing)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 cursor-pointer transition"
                        onclick="window.location='{{ route('listings.show', $listing) }}'">
                        <td class="py-3 px-4">
                            <div class="font-medium text-gray-900">{{ $listing->title }}</div>
                            <div class="text-sm text-gray-500">{{ $listing->category->name ?? 'Без категории' }}</div>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <span class="font-semibold text-gray-900">
                                @if($listing->price_type == 'fixed')
                                    {{ number_format($listing->price, 0, ',', ' ') }} ₽
                                @elseif($listing->price_type == 'hour')
                                    {{ number_format($listing->price, 0, ',', ' ') }} ₽/час
                                @else
                                    Договорная
                                @endif
                            </span>
                        </td>
                    </tr>
                @endforeach
                @if($favoriteListings->count() == 0)
                    <tr>
                        <td colspan="2" class="py-8 text-center text-gray-400">
                            Нет избранных объявлений
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    <div id="reviews" class="bg-white rounded-xl border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Отзывы</h2>
        @if($reviews->count() > 0)
            <div class="space-y-4">
                @foreach($reviews as $review)
                    <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                        <div class="flex items-center justify-between mb-1">
                            <a href="{{ route('profile.user', $review->reviewer) }}" class="font-semibold text-gray-900 hover:text-indigo-600">
                                {{ $review->reviewer->name }}
                            </a>
                            <div class="flex text-yellow-500 text-sm">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating) ★ @else ☆ @endif
                                @endfor
                            </div>
                        </div>
                        <div class="text-xs text-gray-400 mb-2">{{ $review->created_at->format('d.m.Y') }}</div>
                        <p class="text-sm text-gray-700">{{ $review->comment ?? 'Без комментария' }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400 text-center py-4">Пока нет отзывов</p>
        @endif
    </div>
</div>

<div id="avatarModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 max-w-md w-full">
        <h3 class="text-lg font-bold mb-4">Сменить аватар</h3>
        <form action="{{ route('profile.update.avatar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="file" name="avatar" accept="image/*" class="w-full border rounded-lg p-2 mb-4" required>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeAvatarModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Отмена</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Загрузить</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAvatarModal() {
        document.getElementById('avatarModal').classList.remove('hidden');
        document.getElementById('avatarModal').classList.add('flex');
    }
    function closeAvatarModal() {
        document.getElementById('avatarModal').classList.add('hidden');
        document.getElementById('avatarModal').classList.remove('flex');
    }
    
    let currentFilter = 'active';
    function filterListings(filter) {
        currentFilter = filter;
        const activeTable = document.getElementById('activeListings');
        const inactiveTable = document.getElementById('inactiveListings');
        const btnActive = document.getElementById('filterActive');
        const btnInactive = document.getElementById('filterInactive');
        
        if (filter === 'active') {
            activeTable.classList.remove('hidden');
            inactiveTable.classList.add('hidden');
            btnActive.classList.remove('bg-gray-100', 'text-gray-600');
            btnActive.classList.add('bg-indigo-600', 'text-white');
            btnInactive.classList.remove('bg-indigo-600', 'text-white');
            btnInactive.classList.add('bg-gray-100', 'text-gray-600');
        } else {
            activeTable.classList.add('hidden');
            inactiveTable.classList.remove('hidden');
            btnInactive.classList.remove('bg-gray-100', 'text-gray-600');
            btnInactive.classList.add('bg-indigo-600', 'text-white');
            btnActive.classList.remove('bg-indigo-600', 'text-white');
            btnActive.classList.add('bg-gray-100', 'text-gray-600');
        }
    }

    const tables = {
        active: document.getElementById('activeListings'),
        inactive: document.getElementById('inactiveListings'),
        favorite: document.getElementById('favoriteListings')
    };

    const buttons = document.querySelectorAll('.filter-btn');

    function setFilter(filter) {
        Object.keys(tables).forEach(key => {
            if (tables[key]) tables[key].classList.add('hidden');
        });
        if (tables[filter]) tables[filter].classList.remove('hidden');
        
        buttons.forEach(btn => {
            if (btn.dataset.filter === filter) {
                btn.classList.remove('bg-gray-100', 'text-gray-600');
                btn.classList.add('bg-indigo-600', 'text-white');
            } else {
                btn.classList.remove('bg-indigo-600', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-600');
            }
        });
    }

    buttons.forEach(btn => {
        btn.addEventListener('click', () => setFilter(btn.dataset.filter));
    });
</script>
@endsection