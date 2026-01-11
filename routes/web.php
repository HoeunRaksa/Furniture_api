<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'welcome']);

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [PageController::class, 'admin']);
});
