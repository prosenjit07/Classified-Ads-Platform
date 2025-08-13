<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Cache;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_DRAFT = 'draft';
    
    /**
     * Get all available statuses.
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_DRAFT => 'Draft',
        ];
    }
    
    /**
     * Get the status label for the current product.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? 'Unknown';
    }
    
    /**
     * Check if the product is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
    
    /**
     * Check if the product is inactive.
     *
     * @return bool
     */
    public function isInactive(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }
    
    /**
     * Check if the product is a draft.
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }
    
    /**
     * Scope a query to only include active products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
    
    /**
     * Scope a query to only include inactive products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }
    
    /**
     * Scope a query to only include draft products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }
    use SoftDeletes, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'sku',
        'stock_quantity',
        'manage_stock',
        'stock_status',
        'condition',
        'status',
        'is_featured',
        'order',
        'meta_title',
        'meta_description',
        'category_id',
        'brand_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'manage_stock' => 'boolean',
        'is_featured' => 'boolean',
        'order' => 'integer',
        'published_at' => 'datetime',
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['status_label'];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
    
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($product) {
            // Set published_at when status changes to active
            if ($product->isDirty('status') && $product->status === self::STATUS_ACTIVE) {
                $product->published_at = $product->published_at ?? now();
            }
            
            // Clear cache on save
            if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
                Cache::tags(['products', 'product_' . $product->id])->flush();
            } else {
                Cache::forget('products');
                Cache::forget('product_' . $product->id);
            }
        });
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the product details for the product.
     */
    public function details(): HasMany
    {
        return $this->hasMany(ProductDetail::class)->orderBy('order');
    }
    
    /**
     * Get the users who have this product in their wishlist.
     */
    public function wishlistedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists')
            ->withPivot(['notes', 'priority', 'created_at'])
            ->withTimestamps()
            ->orderBy('wishlists.priority', 'desc')
            ->orderBy('wishlists.created_at', 'desc');
    }
    
    /**
     * Get the wishlist items for this product.
     */
    public function wishlistItems(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }
    
    /**
     * Check if the product is in the given user's wishlist.
     *
     * @param  int  $userId
     * @return bool
     */
    public function isInUserWishlist($userId): bool
    {
        if (!$userId) {
            return false;
        }
        
        return $this->wishlistItems()->where('user_id', $userId)->exists();
    }
    
    /**
     * Get the count of users who have this product in their wishlist.
     *
     * @return int
     */
    public function getWishlistCountAttribute(): int
    {
        return $this->wishlistItems()->count();
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    /**
     * Register media conversions.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10);

        $this->addMediaConversion('preview')
            ->width(500)
            ->height(500)
            ->sharpen(10);
    }

    /**
     * Get the effective price (sale price if available, otherwise regular price).
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Check if the product is on sale.
     */
    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    /**
     * Get the discount percentage.
     */
    public function getDiscountPercentageAttribute(): ?float
    {
        if (!$this->is_on_sale) {
            return null;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100, 2);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include in-stock products.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock');
    }
    
    /**
     * Scope a query to filter products based on request parameters.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        return $query->when($filters['category_id'] ?? false, function ($query, $categoryId) {
            $query->where('category_id', $categoryId);
        })
        ->when($filters['brand_id'] ?? false, function ($query, $brandId) {
            $query->where('brand_id', $brandId);
        })
        ->when($filters['min_price'] ?? false, function ($query, $minPrice) {
            $query->where('price', '>=', $minPrice);
        })
        ->when($filters['max_price'] ?? false, function ($query, $maxPrice) {
            $query->where('price', '<=', $maxPrice);
        })
        ->when($filters['condition'] ?? false, function ($query, $condition) {
            $query->where('condition', $condition);
        })
        ->when($filters['search'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
            });
        });
    }
    
    /**
     * Scope a query to sort products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $sortBy
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort($query, $sortBy = null)
    {
        return match($sortBy) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->latest(),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };
    }
}
