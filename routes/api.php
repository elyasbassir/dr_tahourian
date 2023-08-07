<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\controller_auth;
use App\Http\Middleware\token_JWT_valid;

Route::POST('/register', [controller_auth::class, 'register'])->name('register_user');
Route::POST('/login', [controller_auth::class, 'login'])->name('login_user');

// برای چک کردن درست بودن توکن کلاینت
Route::middleware(token_JWT_valid::class)->group(function (){

});
