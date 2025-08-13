<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProductController;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

// Public routes
Route::get('/', function (Request $request) {
    if (!Schema::hasTable('products') || !Schema::hasTable('categories') || !Schema::hasTable('brands')) {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'products' => [],
            'categories' => [],
            'brands' => [],
            'filters' => [],
        ]);
    }
    // Gather filters for UI display
    $filters = $request->only(['search', 'category', 'brand', 'min_price', 'max_price', 'condition', 'sort_by', 'per_page']);

    // Defaults
    $perPage = $request->input('per_page', 12);
    $sortBy = $request->input('sort_by', 'latest');

    // Normalize filters for model scopes
    $scopeFilters = [
        'search' => $request->input('search'),
        'category_id' => $request->input('category_id', $request->input('category')), // support both keys
        'brand_id' => $request->input('brand_id', $request->input('brand')),
        'min_price' => $request->input('min_price'),
        'max_price' => $request->input('max_price'),
        'condition' => $request->input('condition'),
    ];

    // Build query using model scopes (industry-standard approach)
    $products = Product::with(['category', 'brand', 'media'])
        ->active()
        ->filter($scopeFilters)
        ->sort($sortBy)
        ->paginate($perPage)
        ->withQueryString();
    
    // Check wishlist status for each product if user is authenticated
    if (auth()->check()) {
        $products->getCollection()->each(function ($product) {
            $product->is_in_wishlist = auth()->user()->wishlist()->where('product_id', $product->id)->exists();
        });
    }
    
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'products' => $products,
        'categories' => Category::active()->get(['id', 'name']),
        'brands' => Brand::active()->get(['id', 'name']),
        'filters' => $filters
    ]);
})->name('welcome');

// Product details route
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Categories Resource Routes
    Route::resource('categories', 'App\Http\Controllers\Admin\CategoryController')->except(['show']);
    
    // Additional routes for category fields
    Route::post('categories/{category}/fields', [\App\Http\Controllers\Admin\CategoryController::class, 'storeField'])
        ->name('categories.fields.store');
    Route::delete('categories/{category}/fields/{field}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroyField'])
        ->name('categories.fields.destroy');
        
    // Brands Resource Routes
    Route::resource('brands', 'App\Http\Controllers\Admin\BrandController');
    
    // Explicitly define the update route to work with both PUT and POST (for method spoofing)
    Route::match(['put', 'post'], 'brands/{brand}', 'App\Http\Controllers\Admin\BrandController@update')
        ->name('brands.update');
    
    // Products Resource Routes with Media
    Route::resource('products', 'App\Http\Controllers\Admin\ProductController');
    Route::post('products/{product}/media', [\App\Http\Controllers\Admin\ProductController::class, 'storeMedia'])
        ->name('products.media.store');
    Route::delete('products/{product}/media/{media}', [\App\Http\Controllers\Admin\ProductController::class, 'destroyMedia'])
        ->name('products.media.destroy');
});

// Regular user routes
// User routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\User\DashboardController::class, 'index'])
        ->name('dashboard');
        
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Wishlist
    Route::get('/wishlist', [\App\Http\Controllers\User\WishlistController::class, 'index'])
        ->name('wishlist.index');
    
    // Recently Viewed (if implemented)
    // Route::get('/recently-viewed', [\App\Http\Controllers\User\RecentlyViewedController::class, 'index'])
    //     ->name('recently-viewed.index');
});

require __DIR__.'/auth.php';