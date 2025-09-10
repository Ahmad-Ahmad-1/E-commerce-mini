<?php

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\StripeWebhookController;

Route::apiResource('/categories', CategoryController::class)
    ->only(['index', 'show']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware('role:Super Admin')->group(function () {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/users', [UserController::class, 'index']);
        Route::patch('/users/update-role/{user}', [UserController::class, 'updateRoles']);
        Route::apiResource('/categories', CategoryController::class)
            ->only(['store', 'update', 'destroy']);
        Route::apiResource('/products', ProductController::class)
            ->only(['store', 'update', 'destroy']);
        Route::get('/products/my-products', [ProductController::class, 'myProducts']);
    });
    Route::post('/products/{product}/comments', [CommentController::class, 'storeProductComment']);
    Route::patch('/products/{product}/comments/{comment}', [CommentController::class, 'updateProductComment']);
    Route::delete('/products/{product}/comments/{comment}', [CommentController::class, 'destroyProductComment']);
    Route::post('/products/{product}/rating', [RatingController::class, 'storeProductRating']);

    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/rating', [RatingController::class, 'storeUserRating']);
    // Route::post('/users/{user}/comments', [CommentController::class, 'storeUserComment']);

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
        Route::post('/orders',  'store');
        Route::post('/orders/{order}/confirm',  'confirm');
        // Route::post('/orders/{order}/buy-again', 'buyAgain');
    });
});

Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

Route::controller(ProductController::class)->group(function () {
    Route::get('/', 'latestProducts');
    Route::get('/products', 'index');
    Route::get('/products/search', 'search');
    Route::get('/products/{product}', 'show');
});

Route::get('/products/{product}/comments', [CommentController::class, 'indexProductComments']);
Route::get('/products/{product}/{comment}/replies', [CommentController::class, 'indexProductCommentReplies']);

Route::get('/user', function (Request $request) {
    return new UserResource($request->user());
})->middleware('auth:sanctum');
