<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Beautiful furniture showcase routes
|
*/

// Public Pages
Route::get('/', [PageController::class, 'welcome'])->name('home');
Route::get('/shop', [PageController::class, 'shop'])->name('shop');
Route::get('/collection/{slug}', [PageController::class, 'collection'])->name('collection');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// Admin Routes (Protected in production with middleware)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [PageController::class, 'admin'])->name('dashboard');
});

// API Routes
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/featured-products', [PageController::class, 'getFeaturedProducts'])->name('featured');
});