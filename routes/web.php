<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
$router->get('/admin', ['middleware' => ['auth', 'role:admin'], function () {
    return 'Admin only';
}]);
