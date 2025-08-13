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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        if (Schema::hasColumn('products', 'status')) {
            return $query->where('status', self::STATUS_ACTIVE);
        }
        if (Schema::hasColumn('products', 'is_active')) {
            return $query->where('is_active', true);
        }
        return $query;
    }
    
    /**
     * Scope a query to only include inactive products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        if (Schema::hasColumn('products', 'status')) {
            return $query->where('status', self::STATUS_INACTIVE);
        }
        if (Schema::hasColumn('products', 'is_active')) {
            return $query->where('is_active', false);
        }
        return $query;
    }
    
    /**
     * Scope a query to only include draft products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDraft($query)
    {
        if (Schema::hasColumn('products', 'status')) {
            return $query->where('status', self::STATUS_DRAFT);
        }
        return $query;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'sku',
        'quantity',
        'in_stock',
        'is_active',
        'is_featured',
        'condition',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'fields', // For storing dynamic field values
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'quantity' => 'integer',
        'in_stock' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'views' => 'integer',
        'status' => 'string',
        'fields' => 'array',
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['status_label', 'dynamic_fields'];

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
     * Get the product's details.
     */
    public function details(): HasMany
    {
        return $this->hasMany(ProductDetail::class);
    }
    
    // duplicate saveDynamicFields implementation removed
    
    /**
     * Get dynamic field values as key-value pairs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDynamicFieldsAttribute()
    {
        return $this->details->pluck('attribute_value', 'attribute_name');
    }
    
    /**
     * Get a specific dynamic field value.
     *
     * @param string $fieldName
     * @return mixed
     */
    public function getDynamicField(string $fieldName)
    {
        $detail = $this->details->firstWhere('attribute_name', $fieldName);
        return $detail?->attribute_value ?? null;
    }
    
    /**
     * Save dynamic field values for the product.
     *
     * @param array $fields
     * @return void
     */
    public function saveDynamicFields(array $fields): void
    {
        if (empty($fields)) {
            return;
        }
        
        // Normalize input to a consistent structure and persist using ProductDetail columns
        DB::transaction(function () use ($fields) {
            foreach ($fields as $fieldData) {
                if (is_array($fieldData) && (isset($fieldData['attribute_name']) || isset($fieldData['name']))) {
                    $attributeName = $fieldData['attribute_name'] ?? $fieldData['name'] ?? null;
                    $attributeValue = $fieldData['attribute_value'] ?? $fieldData['value'] ?? null;
                    $attributeType = $fieldData['attribute_type'] ?? $fieldData['type'] ?? 'text';
                    $detailId = $fieldData['id'] ?? null;
                } elseif (is_array($fieldData)) {
                    $attributeName = key($fieldData);
                    $attributeValue = current($fieldData);
                    $attributeType = 'text';
                    $detailId = null;
                } else {
                    continue;
                }

                if (empty($attributeName)) {
                    continue;
                }

                $this->details()->updateOrCreate(
                    ['id' => $detailId],
                    [
                        'attribute_name' => (string) $attributeName,
                        'attribute_value' => is_array($attributeValue) ? json_encode($attributeValue) : (string) $attributeValue,
                        'attribute_type' => (string) $attributeType,
                    ]
                );
            }
        });
    }
    
    /**
     * Get validation rules for dynamic fields based on category.
     *
     * @return array
     */
    public function getDynamicFieldRules(): array
    {
        if (!$this->category) {
            return [];
        }
        
        return $this->category->getFieldValidationRules();
    }

    /**
     * Get the users who have this product in their wishlist.
     */
    public function wishlistUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists')
            ->withPivot(['notes', 'priority'])
            ->withTimestamps();
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
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'newest', 'latest' => $query->latest(),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };
    }
}
