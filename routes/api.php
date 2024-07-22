<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\CategorySubCategoryController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ItemOrderController;
use App\Models\Feedback;


Route::prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
});



Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']) -> name('api.login');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
});

Route::prefix('v1')->group(function () {

    //Routes for the products
    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/categories/{id}/products', [ProductController::class, 'getProductsByCategory'])->name('products.category');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::post('/products/{id}/tags', [ProductController::class, 'addTags']);
    Route::get('/products/{id}/similar', [ProductController::class, 'getSimilarProducts']);
    Route::get('/products/{id}/frequently-bought-together', [ProductController::class, 'frequentlyBoughtTogether']);

});
Route::prefix('v1')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}/subcategories', [CategoryController::class, 'getSubcategories']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('v1')->group(function () {
    Route::get('/item-orders', [ItemOrderController::class, 'index']);
    Route::post('/item-orders', [ItemOrderController::class,'store']);
});

Route::prefix('v1')->group(function () {
    Route::post('cart/add', [CartController::class, 'add']);
    Route::post('cart/remove', [CartController::class, 'remove']);
    Route::post('cart/remove/all', [CartController::class, 'removeItemOrder']);
    Route::post('cart/clear', [CartController::class, 'clearCart']);
    Route::get('cart', [CartController::class, 'view']);
    Route::get('cart/items/count', [CartController::class, 'getCartItemsCount']);
});

Route::prefix('v1')->group(function () {
    Route::get('/carts/{id}/item-orders', [CartController::class, 'getCartItemOrders']);
});

Route::prefix('v1')->group(function () {
    Route::get('user/feedbacks', [FeedbackController::class, 'getUserFeedbacks']);
    Route::get('products/{id}/feedbacks', [FeedbackController::class, 'getProductFeedbacks']);

    Route::post('feedbacks', [FeedbackController::class, 'store']);
});