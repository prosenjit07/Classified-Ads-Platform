<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProductController;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public routes
Route::get('/', function (Request $request) {
    // Get filter parameters
    $filters = $request->only(['search', 'category', 'brand', 'min_price', 'max_price', 'condition', 'sort_by', 'per_page']);
    
    // Default values
    $perPage = $request->input('per_page', 12);
    $sortBy = $request->input('sort_by', 'latest');
    
    // Build the query
    $query = Product::with(['category', 'brand', 'media'])
        ->when($request->search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        })
        ->when($request->category, function ($query, $categoryId) {
            $query->where('category_id', $categoryId);
        })
        ->when($request->brand, function ($query, $brandId) {
            $query->where('brand_id', $brandId);
        })
        ->when($request->min_price, function ($query, $minPrice) {
            $query->where('price', '>=', $minPrice * 100); // Convert to cents
        })
        ->when($request->max_price, function ($query, $maxPrice) {
            $query->where('price', '<=', $maxPrice * 100); // Convert to cents
        })
        ->when($request->condition, function ($query, $condition) {
            $query->where('condition', $condition);
        });
    
    // Apply sorting
    switch ($sortBy) {
        case 'price_asc':
            $query->orderBy('price');
            break;
        case 'price_desc':
            $query->orderByDesc('price');
            break;
        case 'name_asc':
            $query->orderBy('name');
            break;
        case 'name_desc':
            $query->orderByDesc('name');
            break;
        case 'latest':
        default:
            $query->latest();
            break;
    }
    
    // Get paginated results
    $products = $query->paginate($perPage)->withQueryString();
    
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