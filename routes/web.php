<?php

use App\Utils\Route;
use App\Controllers\{
    HomeController,
    LoginController,
};

Route::get('/login',[LoginController::class,'showFormLogin']);
Route::post('/login',[LoginController::class,'login'])->name('login');
Route::get('/login/callback',[LoginController::class,'googleCallback']);
Route::get('/logout',[LoginController::class,'logout'])->name('logout');

Route::middeware('auth')->group(function(){

    Route::get('/',[HomeController::class,'index'])->name('home');

});


