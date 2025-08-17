<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'avatar',
        'preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'is_admin',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'preferences' => 'array',
        'is_admin' => 'boolean',
    ];

    /**
     * Get the wishlist items for the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class, 'user_id')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get all wishlist items for the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wishlistItems()
    {
        return $this->wishlist();
    }

    /**
     * Get the user's wishlist products.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists')
            ->withPivot(['notes', 'priority', 'created_at'])
            ->withTimestamps()
            ->orderBy('wishlists.priority', 'desc')
            ->orderBy('wishlists.created_at', 'desc');
    }

    /**
     * Check if a product is in the user's wishlist.
     *
     * @param  int  $productId
     * @return bool
     */
    public function hasInWishlist($productId): bool
    {
        return $this->wishlist()
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Add a product to the user's wishlist.
     *
     * @param  int  $productId
     * @param  string|null  $notes
     * @param  int  $priority
     * @return \App\Models\Wishlist
     */
    public function addToWishlist($productId, $notes = null, $priority = 0): Wishlist
    {
        return $this->wishlistItems()->updateOrCreate(
            ['product_id' => $productId],
            ['notes' => $notes, 'priority' => $priority]
        );
    }

    /**
     * Remove a product from the user's wishlist.
     *
     * @param  int  $productId
     * @return bool
     */
    public function removeFromWishlist($productId): bool
    {
        return (bool) $this->wishlistItems()
            ->where('product_id', $productId)
            ->delete();
    }

    /**
     * Toggle a product in the user's wishlist.
     *
     * @param  int  $productId
     * @param  string|null  $notes
     * @param  int  $priority
     * @return array
     */
    public function toggleWishlist($productId, $notes = null, $priority = 0): array
    {
        return Wishlist::toggleWishlist($this->id, $productId, $notes, $priority);
    }
}
