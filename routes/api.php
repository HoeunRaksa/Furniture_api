<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Bank\BankAccountController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

// Public Product Routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/qr/details/{tranId}', [OrderController::class, 'getTransactionDetails']);
Route::post('/qr/pay/{tranId}', [OrderController::class, 'finalizePayment']); // Added POST support
Route::get('/qr/pay/{tranId}', [OrderController::class, 'finalizePayment']);  // Keep GET support
Route::get('/orders/{invoice_no}/status', [OrderController::class, 'checkStatus']); // Made Public

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Requires Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth Routes
    Route::get('/user', [AuthController::class, 'user']); // ✅ Replaced UserController::me
    Route::post('/update-profile', [AuthController::class, 'updateProfile']); // ✅ Replaced UserController::uploadProfileImage
    Route::delete('/profile-image', [AuthController::class, 'deleteProfileImage']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Favorite Routes
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{product_id}', [FavoriteController::class, 'destroy']);
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle']);
    Route::get('/favorites/check/{product_id}', [FavoriteController::class, 'check']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Dedicated Bank System Routes (Separate Frontend)
|--------------------------------------------------------------------------
*/
Route::prefix('bank')->group(function () {
    // Public Bank Routes
    Route::post('/login', [BankAccountController::class, 'login']);
    Route::get('/seed', [BankAccountController::class, 'seedTestData']);

    // Protected Bank Routes
    Route::middleware('bank.auth')->group(function () {
        Route::get('/account', [BankAccountController::class, 'getAccountDetails']);
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Requires Admin Role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    // Admin Dashboard
    Route::get('/admin/dashboard', fn () => response()->json([
        'success' => true,
        'message' => 'Admin dashboard',
    ]));

    // Product Management Routes
    Route::post('/admin/products', [ProductController::class, 'store']);
    Route::post('/admin/products/{id}', [ProductController::class, 'update']);
    Route::delete('/admin/products/{id}', [ProductController::class, 'destroy']);

    // Product Images Management
    Route::post('/admin/products/{id}/images', [ProductController::class, 'uploadImages']);
    Route::delete('/admin/products/{productId}/images/{imageId}', [ProductController::class, 'deleteImage']);
});
