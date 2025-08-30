<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => 'redirections lead to this route,
probably you are hitting some authentication route (/register, /login, /forgot-password...) while are already authenticated');

Route::post('/reset-password/{token}', fn() => 'Send your token to this route');