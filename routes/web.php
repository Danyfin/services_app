<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;


Route::get('/', [ListingController::class, 'welcome'])->name('welcome');

Route::get('/region/{slug}', [RegionController::class, 'switch'])->name('region.switch');

Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/search', [ListingController::class, 'search'])->name('listings.search');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function() {
        return redirect()->route('profile.show');
    })->name('dashboard');
    
    Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
    Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{listing}', [ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');
    
    // Сообщения
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'dialog'])->name('messages.dialog');
    Route::post('/messages/send', [MessageController::class, 'send'])->name('messages.send');
    Route::post('/messages/poll', [MessageController::class, 'poll'])->name('messages.poll');
    
    // Заказы
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::patch('/orders/{order}/accept', [OrderController::class, 'accept'])->name('orders.accept');
    Route::patch('/orders/{order}/start', [OrderController::class, 'start'])->name('orders.start');
    Route::patch('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
    Route::delete('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/review', [OrderController::class, 'storeReview'])->name('reviews.store');

    // Профиль
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update.avatar');
    Route::get('/profile/{user}', [ProfileController::class, 'userProfile'])->name('profile.user');

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{listing}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
});

Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');

require __DIR__.'/auth.php';