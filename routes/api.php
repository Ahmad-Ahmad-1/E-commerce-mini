<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;

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

    Route::post('/checkout', [CheckoutController::class, 'checkout']);

    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::patch('/cart/{item}', [CartController::class, 'updateQuantity']);
    Route::delete('/cart/{item}', [CartController::class, 'remove']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::patch('/orders/{order}', [OrderController::class, 'cancel']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);

    Route::post('/orders/{order}/checkout', [PaymentController::class, 'checkout']);
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

    // Webhook (for real confirmation)
    // Route::post('/stripe/webhook', [\App\Http\Controllers\WebhookController::class, 'handle']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
