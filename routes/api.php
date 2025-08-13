<?php

use App\Http\Controllers\API\WishlistController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // Wishlist routes
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index']);
        Route::post('/{product}', [WishlistController::class, 'add']);
        Route::delete('/{wishlistItem}', [WishlistController::class, 'remove']);
        Route::put('/{wishlistItem}', [WishlistController::class, 'update']);
        Route::post('/{product}/toggle', [WishlistController::class, 'toggle']);
        Route::delete('/', [WishlistController::class, 'clear']);
    });
    
    // User profile routes
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });
});
