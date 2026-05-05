@extends('layouts.app')

@section('title', $listing->title)

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6 flex-wrap">
            <a href="{{ route('welcome') }}" class="hover:text-sky-600 transition-colors">Главная</a>
            
            @if($listing->category->parent)
                <span>/</span>
                <a href="{{ route('categories.show', $listing->category->parent->slug) }}" class="hover:text-sky-600 transition-colors">
                    {{ $listing->category->parent->name }}
                </a>
            @endif
            
            <span>/</span>
            <a href="{{ route('categories.show', $listing->category->slug) }}" class="hover:text-sky-600 transition-colors">
                {{ $listing->category->name }}
            </a>
            
            <span>/</span>
            <span class="text-gray-900 font-medium truncate max-w-xs">{{ $listing->title }}</span>
        </nav>

        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">{{ $listing->title }}</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="relative bg-gray-900 rounded-xl overflow-hidden" style="aspect-ratio: 16/9;">
                    @php
                        $images = $listing->images;
                    @endphp
                    
                    @if($images->count() > 0)
                        <div id="sliderContainer" class="relative w-full h-full">
                            <img id="sliderImage" src="{{ asset('storage/' . $images->first()->image_path) }}" 
                                 alt="{{ $listing->title }}"
                                 class="w-full h-full object-cover">
                            
                            <div id="imageCounter" class="absolute top-3 right-3 bg-black/60 text-white text-sm px-3 py-1 rounded-full">
                                1 / {{ $images->count() }}
                            </div>
                            
                            <button id="prevSlide" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 hover:bg-white rounded-full flex items-center justify-center text-gray-700 font-bold transition-colors shadow">
                                ‹
                            </button>
                            <button id="nextSlide" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 hover:bg-white rounded-full flex items-center justify-center text-gray-700 font-bold transition-colors shadow">
                                ›
                            </button>
                            
                            <div id="sliderDots" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
                                @foreach($images as $index => $image)
                                    <button data-index="{{ $index }}" class="slider-dot w-2 h-2 rounded-full transition-colors {{ $index == 0 ? 'bg-white' : 'bg-white/50' }}"></button>
                                @endforeach
                            </div>
                        </div>
                        
                        <script>
                            const images = @json($images->map(function($img) { return asset('storage/' . $img->image_path); }));
                            let currentIndex = 0;
                            const sliderImage = document.getElementById('sliderImage');
                            const imageCounter = document.getElementById('imageCounter');
                            const prevSlide = document.getElementById('prevSlide');
                            const nextSlide = document.getElementById('nextSlide');
                            
                            function updateSlider() {
                                sliderImage.src = images[currentIndex];
                                imageCounter.textContent = (currentIndex + 1) + ' / ' + images.length;
                                document.querySelectorAll('.slider-dot').forEach((dot, i) => {
                                    if (i === currentIndex) {
                                        dot.classList.add('bg-white');
                                        dot.classList.remove('bg-white/50');
                                    } else {
                                        dot.classList.remove('bg-white');
                                        dot.classList.add('bg-white/50');
                                    }
                                });
                            }
                            
                            if (prevSlide) prevSlide.addEventListener('click', () => {
                                currentIndex = (currentIndex - 1 + images.length) % images.length;
                                updateSlider();
                            });
                            
                            if (nextSlide) nextSlide.addEventListener('click', () => {
                                currentIndex = (currentIndex + 1) % images.length;
                                updateSlider();
                            });
                            
                            document.querySelectorAll('.slider-dot').forEach((dot, i) => {
                                dot.addEventListener('click', () => {
                                    currentIndex = i;
                                    updateSlider();
                                });
                            });
                        </script>
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400 text-sm">
                            Нет фото
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
                    
                    <div>
                        <span class="text-sm text-gray-500 font-medium">Категория: </span>
                        <a href="{{ route('categories.show', $listing->category->slug) }}" class="text-sm text-sky-600 hover:underline">
                            {{ $listing->category->name }}
                        </a>
                    </div>

                    <div>
                        <h2 class="font-bold text-gray-900 mb-2">Описание:</h2>
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $listing->description }}</p>
                    </div>

                    @if($listing->order_requirements)
                        <div>
                            <h2 class="font-bold text-gray-900 mb-2">Нужно для заказа:</h2>
                            <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $listing->order_requirements }}</div>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                        <div>
                            <div class="text-xs text-gray-500 font-medium mb-1">Цена</div>
                            <div class="text-lg font-bold text-gray-900">
                                @if($listing->price_type == 'negotiable')
                                    Договорная
                                @else
                                    {{ number_format($listing->price, 0, ',', ' ') }} ₽
                                    <span class="text-sm font-normal text-gray-500 ml-1">
                                        @if($listing->price_type == 'hour')/час@endif
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 font-medium mb-1">Регион</div>
                            <div class="text-sm font-medium text-gray-900">{{ $listing->region->name ?? 'Не указан' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 font-medium mb-1">Исполнитель</div>
                            <div class="text-sm font-medium text-gray-900">{{ $listing->user->name }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 font-medium mb-1">Рейтинг</div>
                            <div class="flex items-center gap-1">
                                <div class="flex text-yellow-500">
                                    @php
                                        $rating = $listing->user->rating_avg ?? 0;
                                        $fullStars = floor($rating);
                                        $halfStar = $rating - $fullStars >= 0.5;
                                    @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $fullStars)
                                            ★
                                        @elseif($i == $fullStars + 1 && $halfStar)
                                            ★
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-500">({{ number_format($rating, 1) }})</span>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 font-medium mb-1">Просмотры</div>
                            <div class="text-sm text-gray-700">{{ $listing->views_count }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 font-medium mb-1">Адрес</div>
                            <div class="text-sm text-gray-700">{{ $listing->address ?? 'Не указан' }}</div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        @auth
                        @if($listing->user_id == Auth::id())
                            <a href="{{ route('listings.edit', $listing) }}" 
                            class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white py-3 rounded-xl font-bold text-base text-center transition-colors">
                                Редактировать
                            </a>
                        @else
                                <form action="{{ route('orders.store') }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                                    <textarea name="description" id="orderDescription" rows="2" 
                                              class="hidden w-full rounded-md border-gray-300 mb-2 text-sm"
                                              placeholder="Опишите, что нужно сделать..."></textarea>
                                    <button type="button" id="orderBtn" 
                                            class="w-full bg-sky-500 hover:bg-sky-600 text-white py-3 rounded-xl font-bold text-base transition-colors">
                                        Заказать
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" 
                               class="flex-1 bg-sky-500 hover:bg-sky-600 text-white py-3 rounded-xl font-bold text-base text-center transition-colors">
                                Заказать
                            </a>
                        @endauth
                        @auth
                            <button onclick="toggleFavorite({{ $listing->id }})" 
                                    id="favoriteBtn"
                                    class="px-5 py-3 rounded-xl border-2 font-medium text-sm transition-colors
                                    {{ Auth::user()->hasFavorited($listing->id) 
                                        ? 'border-red-400 text-red-500 bg-red-50' 
                                        : 'border-gray-300 text-gray-600 hover:border-gray-400' }}">
                                {{ Auth::user()->hasFavorited($listing->id) ? '♥ В избранном' : '♡ В избранное' }}
                            </button>
                        @else
                            <a href="{{ route('login') }}" 
                            class="px-5 py-3 rounded-xl border-2 border-gray-300 text-gray-600 font-medium text-sm text-center hover:border-gray-400 transition-colors">
                                ♡ В избранное
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            @auth
            @if($listing->user_id != Auth::id())
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl border border-gray-200 sticky top-20 overflow-hidden">
                        <!-- Шапка чата -->
                        <div class="px-4 py-4 border-b border-gray-100">
                            <h3 class="font-bold text-gray-900 text-sm mb-3">Чат с исполнителем</h3>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                    {{ substr($listing->user->name ?? 'U', 0, 2) }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900"><a href="{{ route('profile.user', $listing->user) }}" class="text-indigo-600 hover:underline">
                                        {{ $listing->user->name }}
                                    </a></div>
                                    <div class="flex items-center gap-1 text-xs text-gray-500">
                                        <div class="flex text-yellow-500 text-xs">
                                            @php
                                                $userRating = $listing->user->rating_avg ?? 0;
                                                for($i = 1; $i <= 5; $i++) {
                                                    echo $i <= floor($userRating) ? '★' : '☆';
                                                }
                                            @endphp
                                        </div>
                                        <span>{{ number_format($userRating, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-auto flex items-center gap-1 text-xs text-green-600">
                                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                    Онлайн
                                </div>
                            </div>
                        </div>

                        <div id="chatMessages" class="px-4 py-4 h-72 overflow-y-auto flex flex-col gap-3 bg-gray-50">
                            @forelse($messages as $message)
                                <div class="flex {{ $message->sender_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[80%] px-3 py-2 rounded-2xl text-sm leading-relaxed 
                                        {{ $message->sender_id == Auth::id() 
                                            ? 'bg-sky-500 text-white rounded-br-sm' 
                                            : 'bg-white text-gray-800 rounded-bl-sm border border-gray-200' }}">
                                        {{ $message->message }}
                                        <div class="text-xs mt-1 {{ $message->sender_id == Auth::id() ? 'text-sky-100' : 'text-gray-400' }}">
                                            {{ $message->created_at->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="flex-1 flex items-center justify-center text-sm text-gray-400 text-center min-h-[200px]">
                                    <div>
                                        <div class="text-3xl mb-2">💬</div>
                                        <p>Начните диалог с мастером</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <div class="px-4 py-3 border-t border-gray-100 bg-white">
                            @auth
                                @if($listing->user_id != Auth::id())
                                    <form id="messageForm" class="flex gap-2">
                                        @csrf
                                        <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                                        <input type="hidden" name="receiver_id" value="{{ $listing->user_id }}">
                                        <input type="text" name="message" id="messageInput" 
                                            placeholder="Написать..."
                                            class="flex-1 px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-300 bg-gray-50">
                                        <button type="submit"
                                                class="w-9 h-9 bg-sky-500 hover:bg-sky-600 text-white rounded-full flex items-center justify-center flex-shrink-0 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <div class="text-center text-xs text-gray-400">
                                        Здесь будут сообщения от заказчиков
                                    </div>
                                @endif
                            @else
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 mb-2">Войдите чтобы написать мастеру</p>
                                    <a href="{{ route('login') }}" class="text-sm text-sky-500 hover:text-sky-700 font-medium">
                                        Войти
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endauth
        
        <div class="mt-12">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Отзывы</h2>
                
                @php
                    $reviews = \App\Models\Review::where('executor_id', $listing->user_id)
                        ->with(['reviewer', 'executor'])
                        ->latest()
                        ->get();
                @endphp
                
                @if($reviews->count() > 0)
                    <div class="space-y-6">
                        @foreach($reviews as $review)
                            <div class="border-b border-gray-100 pb-6 last:border-0 last:pb-0">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                                            {{ substr($review->reviewer->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $review->reviewer->name ?? 'Пользователь' }}</div>
                                            <div class="flex items-center gap-1 text-yellow-500 text-sm">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        ★
                                                    @else
                                                        ☆
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $review->created_at->format('d.m.Y') }}</div>
                                </div>
                                @if($review->comment)
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400">
                        <div class="text-4xl mb-2">📝</div>
                        <p>Нет отзывов</p>
                        <p class="text-sm">Станьте первым, кто оценит этого исполнителя</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    const chatMessages = document.getElementById('chatMessages');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const listingId = {{ $listing->id }};
    const receiverId = {{ $listing->user_id }};
    
    function scrollToBottom() {
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }
    
    setTimeout(scrollToBottom, 100);
    
    if (messageForm) {
        messageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const message = messageInput?.value.trim();
            if (!message) return;
            
            messageInput.disabled = true;
            
            try {
                const response = await fetch('{{ route("messages.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        listing_id: listingId,
                        receiver_id: receiverId,
                        message: message
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    messageInput.value = '';
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'flex justify-end mb-2';
                    messageDiv.innerHTML = `
                        <div class="max-w-[80%] px-3 py-2 rounded-2xl text-sm leading-relaxed bg-sky-500 text-white rounded-br-sm">
                            ${escapeHtml(message)}
                            <div class="text-xs mt-1 text-sky-100">Только что</div>
                        </div>
                    `;
                    chatMessages.appendChild(messageDiv);
                    scrollToBottom();
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                messageInput.disabled = false;
                messageInput.focus();
            }
        });
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    let lastMessageId = {{ $messages->last()?->id ?? 0 }};
    
    async function checkNewMessages() {
        try {
            const response = await fetch('{{ route("messages.poll") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    listing_id: listingId,
                    last_id: lastMessageId
                })
            });
            
            const data = await response.json();
            
            if (data.messages && data.messages.length > 0) {
                for (const msg of data.messages) {
                    if (msg.sender_id != {{ Auth::id() ?? 0 }}) {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'flex justify-start mb-2';
                        messageDiv.innerHTML = `
                            <div class="max-w-[80%] px-3 py-2 rounded-2xl text-sm leading-relaxed bg-white text-gray-800 rounded-bl-sm border border-gray-200">
                                ${escapeHtml(msg.message)}
                                <div class="text-xs mt-1 text-gray-400">${msg.time}</div>
                            </div>
                        `;
                        chatMessages.appendChild(messageDiv);
                        lastMessageId = msg.id;
                        scrollToBottom();
                    }
                }
            }
        } catch (error) {
            console.error('Polling error:', error);
        }
    }
    
    setInterval(checkNewMessages, 3000);
</script>

<script>
    const orderBtn = document.getElementById('orderBtn');
    const orderDescription = document.getElementById('orderDescription');
    
    if (orderBtn && orderDescription) {
        let isOrderMode = false;
        
        orderBtn.addEventListener('click', () => {
            if (!isOrderMode) {
                orderDescription.classList.remove('hidden');
                orderBtn.textContent = 'Подтвердить заказ';
                orderBtn.classList.remove('bg-sky-500', 'hover:bg-sky-600');
                orderBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                isOrderMode = true;
            } else {
                if (orderDescription.value.trim()) {
                    orderBtn.closest('form').submit();
                } else {
                    alert('Пожалуйста, опишите, что нужно сделать');
                }
            }
        });
    }
</script>

<script>
    const favoriteBtn = document.getElementById('favoriteBtn');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', async () => {
            const isFav = favoriteBtn.textContent.includes('♥');
            favoriteBtn.textContent = isFav ? '♡ В избранное' : '♥ В избранном';
            favoriteBtn.classList.toggle('border-red-400');
            favoriteBtn.classList.toggle('text-red-500');
            favoriteBtn.classList.toggle('bg-red-50');
        });
    }
</script>

<script>
function toggleFavorite(listingId) {
    fetch(`/favorites/${listingId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = document.getElementById('favoriteBtn');
            if (data.isFavorited) {
                btn.textContent = '♥ В избранном';
                btn.classList.remove('border-gray-300', 'text-gray-600');
                btn.classList.add('border-red-400', 'text-red-500', 'bg-red-50');
            } else {
                btn.textContent = '♡ В избранное';
                btn.classList.remove('border-red-400', 'text-red-500', 'bg-red-50');
                btn.classList.add('border-gray-300', 'text-gray-600');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endsection