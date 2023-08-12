<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\controller_auth;
use App\Http\Middleware\token_JWT_valid;
use \App\Enums\UserType;


Route::POST('/register', [controller_auth::class, 'register'])->name('register_user')->withoutMiddleware('"throttle:1,20"');
Route::POST('/login', [controller_auth::class, 'login'])->name('login_user');
Route::POST('/active_account',[controller_auth::class,'active_account'])->withoutMiddleware("throttle:3,10");
Route::POST('/get_code_again',[controller_auth::class,'get_code_again'])->withoutMiddleware("throttle:1,60");
Route::POST('/update_user',[controller_auth::class,'update_user']);
// برای چک کردن درست بودن توکن کلاینت
//Route::middleware(token_JWT_valid::class.":".UserType::admin)->group(function (){
//
//});
