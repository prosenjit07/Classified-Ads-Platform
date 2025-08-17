<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Send welcome email to user
     *
     * @param User $user
     * @return void
     */
    public function sendWelcomeEmail(User $user): void
    {
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Throwable $e) {
            Log::error('Failed to send welcome email: ' . $e->getMessage());
            // Fail silently to not block registration
        }
    }

    /**
     * Update user profile
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateProfile(User $user, array $data): User
    {
        $user->fill($data);
        
        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        
        $user->save();
        
        return $user;
    }

    /**
     * Get user's wishlist items
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWishlist(User $user)
    {
        return $user->wishlistProducts()
            ->with(['media', 'category'])
            ->get()
            ->map(function ($product) {
                $product->formatted_price = number_format((float) $product->price, 2);
                if ($product->sale_price) {
                    $product->formatted_sale_price = number_format((float) $product->sale_price, 2);
                }
                return $product;
            });
    }

    /**
     * Add product to user's wishlist
     *
     * @param User $user
     * @param int $productId
     * @param string|null $notes
     * @param int $priority
     * @return array
     */
    public function addToWishlist(User $user, int $productId, ?string $notes = null, int $priority = 0): array
    {
        $existing = $user->wishlist()->where('product_id', $productId)->first();
        
        if ($existing) {
            return [
                'action' => 'removed',
                'message' => 'Product removed from wishlist'
            ];
        }
        
        $user->wishlist()->create([
            'product_id' => $productId,
            'notes' => $notes,
            'priority' => $priority
        ]);
        
        return [
            'action' => 'added',
            'message' => 'Product added to wishlist'
        ];
    }

    /**
     * Remove product from user's wishlist
     *
     * @param User $user
     * @param int $productId
     * @return bool
     */
    public function removeFromWishlist(User $user, int $productId): bool
    {
        return (bool) $user->wishlist()
            ->where('product_id', $productId)
            ->delete();
    }
}
