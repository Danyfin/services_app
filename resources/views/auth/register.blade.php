<x-guest-layout>
    <div class=" flex items-center justify-center py-6 px-4 sm:px-2 lg:px-4">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <a href="{{ route('welcome') }}" class="text-3xl font-bold text-indigo-600">
                    ХЭЛПА
                </a>
                <h2 class="mt-6 text-2xl font-bold text-gray-900">Регистрация</h2>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
                @csrf

                <div class="space-y-4">
                    <!-- Имя -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Имя
                        </label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                               placeholder="Как вас зовут?">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Логин -->
                    <div>
                        <label for="login" class="block text-sm font-medium text-gray-700 mb-1">
                            Логин
                        </label>
                        <input id="login" type="text" name="login" value="{{ old('login') }}" required
                               class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                               placeholder="Придумайте логин">
                        @error('login')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                               placeholder="example@mail.ru">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Телефон -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Телефон
                        </label>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                               class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                               placeholder="+7 (900) 123-45-67">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Пароль -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Пароль
                        </label>
                        <input id="password" type="password" name="password" required
                               class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                               placeholder="Не менее 8 символов">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Подтверждение пароля -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            Подтвердите пароль
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                               placeholder="Повторите пароль">
                    </div>
                </div>
                <div class="w-full flex flex-col items-center justify-center">
                    <button type="submit"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        Зарегистрироваться
                    </button>
                    <p class="mt-2 text-sm text-gray-600">
                        Уже есть аккаунт?
                        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Войти
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>