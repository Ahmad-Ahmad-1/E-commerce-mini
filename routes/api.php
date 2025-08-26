<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CategoryController;

Route::get("/", [ProductController::class, 'latestProducts']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/checkout', CheckoutController::class);

    Route::apiResource('/categories', CategoryController::class);

    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    Route::get("/products/search", [ProductController::class, 'search']);
    Route::apiResource('/products', ProductController::class);

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::patch('/cart/{item}', [CartController::class, 'updateQuantity']);
    Route::delete('/cart/{item}', [CartController::class, 'remove']);

    Route::get('/orders', [OrderController::class, 'index']);     
    Route::get('/orders/{order}', [OrderController::class, 'show']); 
    Route::patch('/orders/{order}', [OrderController::class, 'cancel']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
