<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    
    // Brand Management
    Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class)->except(['show']);
    Route::get('brands/{brand}', [\App\Http\Controllers\Admin\BrandController::class, 'show'])->name('brands.show');
    
    // Product Management
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::post('products/{product}/media', [\App\Http\Controllers\Admin\ProductController::class, 'storeMedia'])->name('products.media.store');
    Route::delete('products/{product}/media/{media}', [\App\Http\Controllers\Admin\ProductController::class, 'destroyMedia'])->name('products.media.destroy');
});

// Regular user routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';