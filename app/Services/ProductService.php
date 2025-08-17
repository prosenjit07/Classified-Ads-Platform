<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService
{
    /**
     * Get a product by its slug with related data
     *
     * @param string $slug
     * @param User|null $user
     * @return array|null
     */
    public function getProductBySlug(string $slug, ?User $user = null): ?array
    {
        try {
            $product = Product::with(['category', 'brand', 'media'])
                ->where('slug', $slug)
                ->firstOrFail();

            return [
                'product' => $product,
                'formatted_price' => number_format($product->price, 2),
                'formatted_sale_price' => $product->sale_price ? number_format($product->sale_price, 2) : null,
                'in_wishlist' => $user ? $user->wishlist()->where('product_id', $product->id)->exists() : false
            ];
        } catch (\Exception $e) {
            Log::error('ProductService@getProductBySlug error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Format product prices for display
     *
     * @param Product $product
     * @return void
     */
    protected function formatProductPrices(Product $product)
    {
        $product->formatted_price = number_format((float) $product->price, 2);
        if ($product->sale_price) {
            $product->formatted_sale_price = number_format((float) $product->sale_price, 2);
        }
    }

    /**
     * Check if product is in user's wishlist
     *
     * @param Product $product
     * @return void
     */
    protected function checkWishlistStatus(Product $product)
    {
        $product->is_in_wishlist = false;
        if (Auth::check()) {
            $product->is_in_wishlist = DB::table('wishlists')
                ->where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->exists();
        }
    }

    /**
     * Get related products
     *
     * @param Product $product
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRelatedProducts(Product $product, $limit = 4)
    {
        $query = Product::with(['media'])
            ->where('id', '!=', $product->id);
            
        if ($product->category_id) {
            $query->where('category_id', $product->category_id);
        }
            
        return $query->inRandomOrder()
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $item->formatted_price = number_format((float) $item->price, 2);
                if ($item->sale_price) {
                    $item->formatted_sale_price = number_format((float) $item->sale_price, 2);
                }
                return $item;
            });
    }

    /**
     * Get featured products
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeaturedProducts($limit = 8)
    {
        return Product::with(['media', 'category'])
            ->where('is_featured', true)
            ->active()
            ->inRandomOrder()
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                $this->formatProductPrices($product);
                return $product;
            });
    }

    /**
     * Get products with filters and pagination
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFilteredProducts(array $filters = [], int $perPage = 12)
    {
        $query = Product::with(['category', 'brand', 'media'])
            ->where('status', 'active')
            ->when(isset($filters['category']), function ($q) use ($filters) {
                $q->whereHas('category', function ($q) use ($filters) {
                    $q->where('slug', $filters['category']);
                });
            })
            ->when(isset($filters['brand']), function ($q) use ($filters) {
                $q->whereHas('brand', function ($q) use ($filters) {
                    $q->where('slug', $filters['brand']);
                });
            })
            ->when(isset($filters['min_price']), function ($q) use ($filters) {
                $q->where('price', '>=', $filters['min_price']);
            })
            ->when(isset($filters['max_price']), function ($q) use ($filters) {
                $q->where('price', '<=', $filters['max_price']);
            })
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $search = '%' . $filters['search'] . '%';
                $q->where('name', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });

        // Apply sorting
        if (isset($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->latest();
                    break;
                case 'featured':
                    $query->where('is_featured', true);
                    break;
                default:
                    // Default sorting
                    $query->latest();
            }
        } else {
            // Default sorting if none specified
            $query->latest();
        }

        return $query->paginate($perPage);
    }
}
