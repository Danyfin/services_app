<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ХЭЛПА - @yield('title', 'Поиск мастеров')</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    @php
        if (!isset($regions)) {
            $regions = App\Models\Region::where('is_active', true)->get();
        }
        if (!isset($currentRegion)) {
            $currentRegion = App\Models\Region::find(session('current_region_id'));
        }
    @endphp
    
    <div class="min-h-screen flex flex-col">
        <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16 gap-4">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('welcome') }}" class="text-2xl font-bold text-indigo-600">
                            ХЭЛПА
                        </a>
                        
                        <div class="relative">
                            <button id="regionButton" class="flex items-center gap-1 text-gray-600 hover:text-indigo-600">
                                <span>{{ $currentRegion->name ?? 'Челябинск' }}</span>
                                <span>▼</span>
                            </button>
                            
                            <div id="regionDropdown" class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                                @foreach($regions as $region)
                                    <a href="{{ route('region.switch', $region->slug) }}" 
                                       class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 {{ (isset($currentRegion) && $currentRegion->id == $region->id) ? 'bg-indigo-50 text-indigo-600' : '' }}">
                                        {{ $region->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('messages.index') }}" class="text-gray-700 hover:text-indigo-600">
                                Сообщения
                            </a>
                            <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-indigo-600">
                                Мои заказы
                            </a>
                            <a href="{{ route('profile.show') }}" class="text-gray-700 hover:text-indigo-600">
                                Профиль
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-gray-700 hover:text-indigo-600">
                                    Выйти
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600">
                                Вход
                            </a>
                            <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                                Регистрация
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow">
            @yield('content')
        </main>

        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center text-gray-500 text-sm">
                <div class="flex justify-between items-center h-16 gap-4">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('welcome') }}" class="text-2xl font-bold text-indigo-600">
                            ХЭЛПА
                        </a>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('messages.index') }}" class="text-gray-700 hover:text-indigo-600">
                                Сообщения
                            </a>
                            <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-indigo-600">
                                Мои заказы
                            </a>
                            <a href="{{ route('profile.show') }}" class="text-gray-700 hover:text-indigo-600">
                                Профиль
                            </a>
                        @else
                        @endauth
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        const regionButton = document.getElementById('regionButton');
        const regionDropdown = document.getElementById('regionDropdown');
        
        if (regionButton && regionDropdown) {
            regionButton.addEventListener('click', () => {
                regionDropdown.classList.toggle('hidden');
            });
            
            document.addEventListener('click', (e) => {
                if (!regionButton.contains(e.target) && !regionDropdown.contains(e.target)) {
                    regionDropdown.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>