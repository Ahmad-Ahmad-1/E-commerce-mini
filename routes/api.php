<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;

Route::apiResource('/categories', CategoryController::class)
    ->only(['index', 'show']);

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::middleware('role:Super Admin')->group(function () {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/users', [UserController::class, 'index']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::patch('/users/update-role/{user}', [UserController::class, 'updateRoles']);
        Route::apiResource('/categories', CategoryController::class)
            ->only(['store', 'update', 'destroy']);
        Route::apiResource('/products', ProductController::class)
            ->only(['store', 'update', 'destroy']);
        Route::get('/products/my-products', [ProductController::class, 'myProducts']);
    });

    Route::patch('/users/{user}', [UserController::class, 'update']);
    Route::get('/users/{user}', [UserController::class, 'show']);

    Route::controller(CartController::class)->group(function () {
        Route::get('/cart', 'index');
        Route::post('/cart/add', 'add');
        Route::patch('/cart/{item}', 'updateQuantity');
        Route::delete('/cart/{item}', 'remove');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders/my-orders', 'myOrders');
        Route::get('/orders/{order}', 'show');
        Route::patch('/orders/{order}', 'cancel');
        Route::post('/orders/{order}/buy-again', 'buyAgain');
    });

    Route::post('/checkout/create-payment-intent', [CheckoutController::class, 'createPaymentIntent']);
    // Confirm payment after Stripe.js finishes
    Route::post('/payment/confirm', [PaymentController::class, 'confirm']);
});

Route::controller(ProductController::class)->group(function () {
    Route::get('/', 'latestProducts');
    Route::get('/products', 'index');
    Route::get('/products/search', 'search');
    Route::get('/products/{product}', 'show');
    // Route::post('/posts/{post}/like', [LikeController::class, 'toggle']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
