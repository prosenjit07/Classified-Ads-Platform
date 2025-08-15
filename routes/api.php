<?php

use App\Http\Controllers\API\WishlistController;
use App\Http\Controllers\API\ProductController as ApiProductController;
use Illuminate\Support\Facades\Route;



// Public routes (no authentication required)
Route::get('/products', [ApiProductController::class, 'index']);
Route::get('/products/{product:slug}', [ApiProductController::class, 'show']);

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // Wishlist routes
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index']);
        Route::post('/{product}', [WishlistController::class, 'store']);
        Route::delete('/{wishlistItem}', [WishlistController::class, 'destroy']);
        Route::put('/{wishlistItem}', [WishlistController::class, 'update']);
        Route::post('/{product}/toggle', [WishlistController::class, 'toggle']);
        Route::delete('/', [WishlistController::class, 'clear']);
    });
    
    // User profile routes
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });
});
