@extends('layouts.app')

@section('title', 'Сообщения')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden" style="height: calc(100vh - 150px);">
        <div class="flex h-full">
            
            <div class="w-80 border-r border-gray-200 flex flex-col h-full">
                <div class="p-4 border-b border-gray-200 bg-gray-50 flex-shrink-0">
                    <h2 class="font-semibold text-gray-800">Сообщения</h2>
                </div>
                
                <div class="flex-1 overflow-y-auto">
                    @forelse($dialogs as $dialog)
                        <a href="{{ route('messages.dialog', $dialog->id) }}" 
                           class="block p-4 border-b border-gray-100 hover:bg-gray-50 transition {{ isset($otherUser) && $otherUser->id == $dialog->id ? 'bg-indigo-50' : '' }}">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold flex-shrink-0 ">
                                    @if($otherUser->avatar)
                                        <img src="{{ asset('storage/' . $otherUser->avatar) }}" class="w-full h-full object-cover rounded-full">
                                    @else
                                        {{ substr($otherUser->name ?? 'U', 0, 1) }}
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-medium text-gray-900 truncate">{{ $dialog->name }}</h4>
                                        @if($dialog->last_message)
                                            <span class="text-xs text-gray-400 flex-shrink-0 ml-2">{{ $dialog->last_message->created_at->format('d.m H:i') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 truncate mt-0.5">
                                        @if($dialog->last_message)
                                            {{ Str::limit($dialog->last_message->message, 35) }}
                                        @else
                                            Нет сообщений
                                        @endif
                                    </p>
                                    @if(property_exists($dialog, 'listing') && $dialog->listing)
                                        <p class="text-xs text-gray-400 truncate mt-1">
                                            {{ Str::limit($dialog->listing->title, 30) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @if($dialog->unread_count > 0)
                                <div class="mt-2">
                                    <span class="bg-indigo-600 text-white text-xs rounded-full px-2 py-0.5">
                                        {{ $dialog->unread_count }}
                                    </span>
                                </div>
                            @endif
                        </a>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <p>Нет диалогов</p>
                            <p class="text-sm mt-2">Напишите автору объявления, чтобы начать общение</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <div class="flex-1 flex flex-col h-full">
                @if(isset($otherUser) && isset($messages))
                    <div class="p-4 border-b border-gray-200 bg-gray-50 flex-shrink-0">
                        <div class="flex items-center gap-3">
                            <!-- Аватар собеседника -->
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold overflow-hidden">
                                @if($otherUser->avatar)
                                    <img src="{{ asset('storage/' . $otherUser->avatar) }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr($otherUser->name ?? 'U', 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('profile.user', $otherUser) }}" class="font-semibold text-gray-900 hover:text-indigo-600">
                                    {{ $otherUser->name }}
                                </a>
                                <div class="flex items-center gap-1 text-xs">
                                    <div class="flex text-yellow-500">
                                        @php
                                            $rating = $otherUser->rating_avg ?? 0;
                                            for($i = 1; $i <= 5; $i++) {
                                                echo $i <= floor($rating) ? '★' : '☆';
                                            }
                                        @endphp
                                    </div>
                                    <span class="text-gray-500">{{ number_format($rating, 1) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="chatMessages" class="flex-1 overflow-y-auto p-4 bg-gray-50">
                        <div class="space-y-3">
                            @foreach($messages as $message)
                                <div class="flex {{ $message->sender_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[70%] {{ $message->sender_id == Auth::id() ? 'bg-indigo-600 text-white' : 'bg-white' }} rounded-lg p-3 shadow-sm">
                                        <p class="text-sm break-words">{{ $message->message }}</p>
                                        <span class="text-xs {{ $message->sender_id == Auth::id() ? 'text-indigo-200' : 'text-gray-400' }} mt-1 block text-right">
                                            {{ $message->created_at->format('H:i') }}
                                            @if($message->sender_id == Auth::id())
                                                @if($message->is_read) ✓✓ @else ✓ @endif
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="p-4 border-t border-gray-200 bg-white flex-shrink-0">
                        <form id="messageForm" class="flex gap-2">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
                            <input type="text" name="message" id="messageInput" 
                                   placeholder="Написать сообщение..." 
                                   class="flex-1 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                                   autocomplete="off">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                                Отправить
                            </button>
                        </form>
                    </div>
                    
                    <script>
                        const chatMessages = document.getElementById('chatMessages');
                        const messageForm = document.getElementById('messageForm');
                        const messageInput = document.getElementById('messageInput');
                        const receiverId = {{ $otherUser->id }};
                        
                        function scrollToBottom() {
                            if (chatMessages) {
                                chatMessages.scrollTop = chatMessages.scrollHeight;
                            }
                        }
                        
                        setTimeout(scrollToBottom, 100);
                        
                        if (messageForm) {
                            messageForm.addEventListener('submit', async function(e) {
                                e.preventDefault();
                                
                                const message = messageInput.value.trim();
                                if (!message) return;
                                
                                messageInput.disabled = true;
                                
                                try {
                                    let listingId = null;
                                    @if(isset($messages) && $messages->first())
                                        listingId = {{ $messages->first()->listing_id ?? 0 }};
                                    @endif
                                    
                                    const response = await fetch('{{ route("messages.send") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            listing_id: listingId || 1,
                                            receiver_id: receiverId,
                                            message: message
                                        })
                                    });
                                    
                                    const data = await response.json();
                                    
                                    if (data.success) {
                                        messageInput.value = '';
                                        
                                        const messagesContainer = chatMessages.querySelector('.space-y-3');
                                        const newMessageDiv = document.createElement('div');
                                        newMessageDiv.className = 'flex justify-end mb-2';
                                        newMessageDiv.innerHTML = `
                                            <div class="max-w-[70%] bg-indigo-600 text-white rounded-lg p-3 shadow-sm">
                                                <p class="text-sm break-words">${escapeHtml(message)}</p>
                                                <span class="text-xs text-indigo-200 mt-1 block text-right">Только что ✓</span>
                                            </div>
                                        `;
                                        messagesContainer.appendChild(newMessageDiv);
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
                                let listingId = null;
                                @if(isset($messages) && $messages->first())
                                    listingId = {{ $messages->first()->listing_id ?? 0 }};
                                @endif
                                
                                const response = await fetch('{{ route("messages.poll") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        listing_id: listingId || 1,
                                        last_id: lastMessageId
                                    })
                                });
                                
                                const data = await response.json();
                                
                                if (data.messages && data.messages.length > 0) {
                                    const messagesContainer = chatMessages.querySelector('.space-y-3');
                                    for (const msg of data.messages) {
                                        if (msg.sender_id != {{ Auth::id() }}) {
                                            const messageDiv = document.createElement('div');
                                            messageDiv.className = 'flex justify-start mb-2';
                                            messageDiv.innerHTML = `
                                                <div class="max-w-[70%] bg-white rounded-lg p-3 shadow-sm">
                                                    <p class="text-sm break-words">${escapeHtml(msg.message)}</p>
                                                    <span class="text-xs text-gray-400 mt-1 block text-right">${msg.time}</span>
                                                </div>
                                            `;
                                            messagesContainer.appendChild(messageDiv);
                                            lastMessageId = msg.id;
                                        }
                                    }
                                    scrollToBottom();
                                }
                            } catch (error) {
                                console.error('Polling error:', error);
                            }
                        }
                        
                        setInterval(checkNewMessages, 3000);
                        messageInput.focus();
                    </script>
                @else
                    <div class="flex-1 flex items-center justify-center text-gray-400">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p>Выберите диалог из списка слева</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection