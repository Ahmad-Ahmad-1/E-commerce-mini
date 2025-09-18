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
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/update-role/{user}', [UserController::class, 'updateRoles'])
            ->name('users.updateRoles');
        Route::apiResource('/categories', CategoryController::class)
            ->only(['store', 'update', 'destroy']);
        Route::apiResource('/products', ProductController::class)
            ->only(['store', 'update', 'destroy']);
        Route::get('/products/my-products', [ProductController::class, 'myProducts'])
            ->name('users.myProducts');
    });
    Route::post('/products/{product}/comments', [CommentController::class, 'storeProductComment']);
    Route::patch('/products/{product}/comments/{comment}', [CommentController::class, 'updateProductComment']);
    Route::delete('/products/{product}/comments/{comment}', [CommentController::class, 'destroyProductComment']);
    Route::post('/products/{product}/ratings', [RatingController::class, 'storeProductRating']);

    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/ratings', [RatingController::class, 'storeUserRating'])
        ->name('users.storeUserRating');
    // Route::post('/users/{user}/comments', [CommentController::class, 'storeUserComment']);

    Route::controller(CartController::class)->group(function () {
        Route::get('/cart', 'index')->name('cart.index');
        Route::post('/cart/add', 'add')->name('cart.add');
        Route::patch('/cart/{item}', 'updateQuantity')->name('cart.updateQuantity');
        Route::delete('/cart/{item}', 'remove')->name('cart.remove');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders/my-orders', 'myOrders')->name('orders.myOrders');
        // Route::get('/orders/{order}', 'show')->name('orders.show');
        Route::get('/orders/{order}/items', 'items')->name('orders.items');
        Route::patch('/orders/{order}', 'cancel')->name('orders.cancel');
        Route::post('/orders',  'store')->name('orders.store');
        Route::post('/orders/{order}/confirm',  'confirm')->name('orders.confirm');
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
    // return $request->url();
    // return ProductListResource::collection(Product::latest()->paginate(2));
    return new UserResource($request->user());
})->middleware('auth:sanctum');
