@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-100 p-6 mb-8">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 flex-shrink-0">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-3xl font-bold text-gray-500">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mt-1 flex-wrap">
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
                        <div class="grid grid-cols-2 gap-4 mt-3">
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
                                <div class="text-xs text-gray-400 mb-1">О ПРОДАВЦЕ</div>
                                <p class="text-sm text-gray-700">{{ $user->about }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden mb-8">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900">Предложения</h2>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500">Услуга</th>
                            <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 w-32">Цена</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($listings as $listing)
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
                    </tbody>
                </table>
            </div>
            
            <!-- Отзывы -->
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
        
        <!-- Правая колонка: чат -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-100 sticky top-20 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-800">Чат с продавцом</h3>
                </div>
                
                <div id="chatMessages" class="h-96 overflow-y-auto p-4 space-y-3 bg-gray-50">
                    @forelse($messages ?? [] as $message)
                        <div class="flex {{ $message->sender_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] {{ $message->sender_id == Auth::id() ? 'bg-indigo-600 text-white' : 'bg-white' }} rounded-lg p-2 shadow-sm">
                                <p class="text-sm break-words">{{ $message->message }}</p>
                                <span class="text-xs {{ $message->sender_id == Auth::id() ? 'text-indigo-200' : 'text-gray-400' }} mt-1 block">
                                    {{ $message->created_at->format('H:i') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 text-sm py-8">
                            <div class="text-3xl mb-2">💬</div>
                            <p>Напишите продавцу, чтобы начать диалог</p>
                        </div>
                    @endforelse
                </div>
                
                <div class="p-4 border-t border-gray-100 bg-white">
                    <form id="messageForm" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                        <input type="text" name="message" id="messageInput" 
                               placeholder="Написать..."
                               class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm">
                            Отправить
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const chatMessages = document.getElementById('chatMessages');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const receiverId = {{ $user->id }};
    let lastMessageId = {{ $messages->last()?->id ?? 0 }};
    
    function scrollToBottom() {
        if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    setTimeout(scrollToBottom, 100);
    
    if (messageForm) {
        messageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const message = messageInput?.value.trim();
            if (!message) return;
            messageInput.disabled = true;
            try {
                let listingId = null;
                const response = await fetch('{{ route("messages.send") }}', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    body: JSON.stringify({listing_id: 1, receiver_id: receiverId, message: message})
                });
                const data = await response.json();
                if (data.success) {
                    messageInput.value = '';
                    const msgDiv = document.createElement('div');
                    msgDiv.className = 'flex justify-end mb-2';
                    msgDiv.innerHTML = `<div class="max-w-[80%] bg-indigo-600 text-white rounded-lg p-2 shadow-sm">
                        <p class="text-sm break-words">${escapeHtml(message)}</p>
                        <span class="text-xs text-indigo-200 mt-1 block">Только что</span>
                    </div>`;
                    chatMessages.appendChild(msgDiv);
                    scrollToBottom();
                }
            } catch(err) { console.error(err); }
            finally { messageInput.disabled = false; messageInput.focus(); }
        });
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    async function checkNewMessages() {
        try {
            const response = await fetch('{{ route("messages.poll") }}', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({listing_id: 1, last_id: lastMessageId})
            });
            const data = await response.json();
            if (data.messages && data.messages.length > 0) {
                for (const msg of data.messages) {
                    if (msg.sender_id != {{ Auth::id() }}) {
                        const msgDiv = document.createElement('div');
                        msgDiv.className = 'flex justify-start mb-2';
                        msgDiv.innerHTML = `<div class="max-w-[80%] bg-white rounded-lg p-2 shadow-sm">
                            <p class="text-sm break-words">${escapeHtml(msg.message)}</p>
                            <span class="text-xs text-gray-400 mt-1 block">${msg.time}</span>
                        </div>`;
                        chatMessages.appendChild(msgDiv);
                        lastMessageId = msg.id;
                        scrollToBottom();
                    }
                }
            }
        } catch(err) { console.error(err); }
    }
    setInterval(checkNewMessages, 3000);
    messageInput?.focus();
</script>
@endsection