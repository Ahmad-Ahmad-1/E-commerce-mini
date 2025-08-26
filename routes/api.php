<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

route::get('/', [HomeController::class, 'home']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'login_home'])->name('dashboard');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    route::apiResource('/categories', CategoryController::class);

    Route::get("/products/search", [ProductController::class, 'search']);
    Route::apiResource('/products', ProductController::class);

    

});

route::get('admin/dashboard', [HomeController::class, 'index'])->middleware(['auth', 'admin']);

route::get('product-details/{id}', [HomeController::class, 'product_details']);
route::get('add-cart/{id}', [HomeController::class, 'add_cart'])->middleware(['auth', 'verified']);
route::get('invoice', [HomeController::class, 'get_invoice']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
