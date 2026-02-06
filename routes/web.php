<?php

use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\WebAuthController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\AttributeController;
use App\Http\Controllers\Web\OrdersController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\BusinessController;
use App\Http\Controllers\Web\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Beautiful furniture showcase routes
|
*/

// Public Pages
Route::get('/', function () {
    return \Illuminate\Support\Facades\Auth::check() ? redirect()->route('home') : redirect()->route('login');
});

// Auth Routes
Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('home');

    // Notification Polling
    Route::get('/notifications/check', [NotificationController::class, 'checkNewOrders'])->name('notifications.check');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Additional admin routes can go here
    });

    // Category Routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/data', [CategoryController::class, 'data'])->name('data');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::put('/update/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // Product Routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/data', [ProductController::class, 'data'])->name('data');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ProductController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [ProductController::class, 'destroy'])->name('destroy');
        Route::delete('/image/delete/{id}', [ProductController::class, 'deleteImage'])->name('image.delete');
    });

    /*
    // Attribute Routes
    Route::prefix('attributes')->name('attributes.')->group(function () {
        Route::get('/', [AttributeController::class, 'index'])->name('index');
        Route::get('/data', [AttributeController::class, 'data'])->name('data');
        Route::post('/store', [AttributeController::class, 'store'])->name('store');
        Route::put('/update/{id}', [AttributeController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [AttributeController::class, 'destroy'])->name('destroy');

        // Values
        Route::get('/values/{id}', [AttributeController::class, 'getValues'])->name('values');
        Route::post('/value/store', [AttributeController::class, 'storeValue'])->name('value.store');
        Route::delete('/value/destroy/{id}', [AttributeController::class, 'destroyValue'])->name('value.destroy');
    });
    */

    // Orders Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrdersController::class, 'index'])->name('index');
        Route::get('/data', [OrdersController::class, 'data'])->name('data');
        Route::get('/show/{id}', [OrdersController::class, 'show'])->name('show');
        Route::delete('/destroy/{id}', [OrdersController::class, 'destroy'])->name('destroy');
    });

    // User Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/data', [UserController::class, 'data'])->name('data');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Business Routes
    Route::prefix('business')->name('business.')->group(function () {
        Route::get('/', [BusinessController::class, 'index'])->name('index');
        Route::post('/update', [BusinessController::class, 'update'])->name('update');
    });
});

// API Routes (Public featured)
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/featured-products', [PageController::class, 'getFeaturedProducts'])->name('featured');
});
