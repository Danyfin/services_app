@extends('layouts.app')

@section('title', 'Редактировать объявление')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Редактировать объявление</h1>
        
        <form method="POST" action="{{ route('listings.update', $listing) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Заголовок *</label>
                <input type="text" name="title" id="title" value="{{ old('title', $listing->title) }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                       required>
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Категория *</label>
                <select name="category_id" id="category_id" class="w-full rounded-md border-gray-300 shadow-sm" required>
                    <option value="">Выберите категорию</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $listing->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Описание *</label>
                <textarea name="description" id="description" rows="6" 
                          class="w-full rounded-md border-gray-300 shadow-sm"
                          required>{{ old('description', $listing->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Цена</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $listing->price) }}" step="0.01"
                           class="w-full rounded-md border-gray-300 shadow-sm">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="price_type" class="block text-sm font-medium text-gray-700 mb-1">Тип цены *</label>
                    <select name="price_type" id="price_type" class="w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="fixed" {{ old('price_type', $listing->price_type) == 'fixed' ? 'selected' : '' }}>Фиксированная</option>
                        <option value="hour" {{ old('price_type', $listing->price_type) == 'hour' ? 'selected' : '' }}>За час</option>
                        <option value="negotiable" {{ old('price_type', $listing->price_type) == 'negotiable' ? 'selected' : '' }}>Договорная</option>
                    </select>
                    @error('price_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mb-4">
                <label for="region_id" class="block text-sm font-medium text-gray-700 mb-1">Регион</label>
                <select name="region_id" id="region_id" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Выберите регион</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" {{ old('region_id', $listing->region_id) == $region->id ? 'selected' : '' }}>
                            {{ $region->name }}
                        </option>
                    @endforeach
                </select>
                @error('region_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Адрес (необязательно)</label>
                <input type="text" name="address" id="address" value="{{ old('address', $listing->address) }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm">
                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="order_requirements" class="block text-sm font-medium text-gray-700 mb-1">Что нужно для заказа</label>
                <textarea name="order_requirements" id="order_requirements" rows="4" 
                          class="w-full rounded-md border-gray-300 shadow-sm"
                          placeholder="Например: описать идею, прикрепить референсы...">{{ old('order_requirements', $listing->order_requirements) }}</textarea>
                @error('order_requirements')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ old('is_active', $listing->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 shadow-sm">
                    <span class="ml-2 text-sm text-gray-700">Объявление активно</span>
                </label>
            </div>
            
            @if($listing->images->count() > 0)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Текущие фотографии</label>
                    <div class="grid grid-cols-3 gap-4">
                        @foreach($listing->images as $image)
                            <div class="relative">
                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                     class="w-full h-32 object-cover rounded-lg">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <div class="mb-4">
                <label for="images" class="block text-sm font-medium text-gray-700 mb-1">Добавить новые фото</label>
                <input type="file" name="images[]" id="images" multiple accept="image/*"
                       class="w-full rounded-md border-gray-300 shadow-sm">
                <p class="text-sm text-gray-500 mt-1">Можно выбрать несколько файлов (jpg, png, до 5МБ)</p>
                @error('images.*')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ route('listings.show', $listing) }}" class="px-4 py-2 border rounded-md hover:bg-gray-50">
                    Отмена
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</div>
@endsection