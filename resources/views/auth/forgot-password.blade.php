<x-guest-layout>
    <div class=" flex items-center justify-center py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <a href="{{ route('welcome') }}" class="text-3xl font-bold text-indigo-600">
                    ХЭЛПА
                </a>
                <h2 class="mt-6 text-2xl font-bold text-gray-900">Восстановление пароля</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Введите ваш email, мы отправим ссылку для сброса пароля
                </p>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('password.email') }}">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                           placeholder="example@mail.ru">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        Отправить ссылку
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                        <- Вернуться ко входу
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>