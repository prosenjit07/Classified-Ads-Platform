<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'notes',
        'priority',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'priority' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the wishlist item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that is in the wishlist.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * Scope a query to only include items for a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include items with a specific product.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $productId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Check if a product is in the user's wishlist.
     *
     * @param  int  $userId
     * @param  int  $productId
     * @return bool
     */
    public static function isInWishlist($userId, $productId): bool
    {
        return static::query()
            ->forUser($userId)
            ->forProduct($productId)
            ->exists();
    }

    /**
     * Add a product to the user's wishlist.
     *
     * @param  int  $userId
     * @param  int  $productId
     * @param  string|null  $notes
     * @param  int  $priority
     * @return \App\Models\Wishlist
     */
    public static function addToWishlist($userId, $productId, $notes = null, $priority = 0): self
    {
        return static::updateOrCreate(
            ['user_id' => $userId, 'product_id' => $productId],
            ['notes' => $notes, 'priority' => $priority]
        );
    }

    /**
     * Remove a product from the user's wishlist.
     *
     * @param  int  $userId
     * @param  int  $productId
     * @return bool
     */
    public static function removeFromWishlist($userId, $productId): bool
    {
        return (bool) static::query()
            ->forUser($userId)
            ->forProduct($productId)
            ->delete();
    }

    /**
     * Toggle a product in the user's wishlist.
     *
     * @param  int  $userId
     * @param  int  $productId
     * @param  string|null  $notes
     * @param  int  $priority
     * @return array
     */
    public static function toggleWishlist($userId, $productId, $notes = null, $priority = 0): array
    {
        $inWishlist = static::isInWishlist($userId, $productId);
        
        if ($inWishlist) {
            static::removeFromWishlist($userId, $productId);
            return ['action' => 'removed', 'in_wishlist' => false];
        }
        
        static::addToWishlist($userId, $productId, $notes, $priority);
        return ['action' => 'added', 'in_wishlist' => true];
    }
}
