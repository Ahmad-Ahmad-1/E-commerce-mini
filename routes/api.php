<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CategoryController;

Route::controller(ProductController::class)->group(function () {
    Route::get('/', 'latestProducts');
    Route::get('/products', 'index');
    Route::get('/products/search', 'search');
    Route::get('/products/{product}', 'show');
});

Route::apiResource('/categories', CategoryController::class)
    ->only(['index', 'show']);

Route::middleware('auth:sanctum')->group(function () {

    Route::middleware('role:Super Admin')->group(function () {
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::patch('/users/{user}', [UserController::class, 'update']);
        Route::apiResource('/categories', CategoryController::class)
            ->only(['store', 'update', 'destroy']);
        Route::apiResource('/products', ProductController::class)
            ->only(['store', 'update', 'destroy']);
    });

    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    Route::controller(CartController::class)->group(function () {
        Route::get('/cart', 'index');
        Route::post('/cart/add', 'add');
        Route::patch('/cart/{item}', 'updateQuantity');
        Route::delete('/cart/{item}', 'remove');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders', 'index');
        Route::get('/orders/{order}', 'show');
        Route::patch('/orders/{order}', 'cancel');
        Route::delete('/orders/{order}', 'destroy');
    });

    Route::post('/checkout/create-session', [CheckoutController::class, 'createSession']);
    Route::get('/checkout/success', [CheckoutController::class, 'success']);
});

Route::get('/success', [CheckoutController::class, 'success'])->name('success');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
