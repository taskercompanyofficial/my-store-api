<?php

use App\Http\Controllers\Authenticated\ProductsController;
use App\Http\Controllers\Authenticated\UsersController;
use App\Http\Controllers\Authentication\AuthenticatedSessionController;
use App\Http\Controllers\Authentication\GoogleLoginController;
use App\Http\Controllers\Authentication\RegisterController;
use App\Http\Controllers\CartItemsController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/google', [GoogleLoginController::class, 'handleGoogleLogin']);

Route::post('/auth/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/auth/check', [AuthenticatedSessionController::class, 'check']);

Route::apiResource('/files', FilesController::class);
Route::post('/auth/register', [RegisterController::class, 'store']);

// Products routes - public access for fetching products
Route::get('/products', [ProductsController::class, 'index']);
Route::get('/products/{product}', [ProductsController::class, 'show']);
Route::get('/settings/{key}', [SettingsController::class, 'show']);
// Protected routes that require authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/products', [ProductsController::class, 'store']);
    Route::put('/products/{product}', [ProductsController::class, 'update']);
    Route::delete('/products/{product}', [ProductsController::class, 'destroy']);

    // Cart routes - authenticated access only
    Route::get('/cart', [CartItemsController::class, 'index']);
    Route::post('/cart', [CartItemsController::class, 'store']);
    Route::put('/cart/{cartItem}', [CartItemsController::class, 'update']);
    Route::delete('/cart/{cartItem}', [CartItemsController::class, 'destroy']);
    Route::delete('/cart', [CartItemsController::class, 'clearCart']);
    Route::get('/cart/check/{product_id}', [CartItemsController::class, 'checkInCart']);

    // Order routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);

    // Users routes
    Route::apiResource('/users', UsersController::class);
    // Settings routes
    Route::apiResource('/admin/settings', SettingsController::class);
});
