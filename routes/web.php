<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => 'You are hitting a guest route (/register, /login, /forgot-password...) while are already authenticated.');

Route::post('/reset-password/{token}', fn() => 'Send your token to this route');