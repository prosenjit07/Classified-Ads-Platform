<?php

namespace App\Services;

use App\Models\Wishlist;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WishlistService
{
    /**
     * Get user's wishlist items with pagination
     *
     * @param User $user
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUserWishlist(User $user, int $perPage = 12): LengthAwarePaginator
    {
        return $user->wishlist()
            ->with(['product.category', 'product.brand', 'product.media'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Add a product to user's wishlist
     *
     * @param User $user
     * @param array $data
     * @return array
     */
    public function addToWishlist(User $user, array $data): array
    {
        try {
            DB::beginTransaction();
            
            // Check if product is already in wishlist
            $existingItem = $this->getWishlistItem($user, $data['product_id']);
            
            if ($existingItem) {
                return [
                    'success' => false,
                    'message' => 'This product is already in your wishlist.',
                    'data' => $existingItem,
                    'status' => 409
                ];
            }
            
            // Create new wishlist item
            $wishlistItem = $user->wishlist()->create([
                'product_id' => $data['product_id'],
                'notes' => $data['notes'] ?? null,
                'priority' => $data['priority'] ?? 0,
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Product added to wishlist successfully.',
                'data' => $wishlistItem->load('product'),
                'status' => 201
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add product to wishlist: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to add product to wishlist. Please try again.',
                'status' => 500
            ];
        }
    }

    /**
     * Toggle product in user's wishlist
     *
     * @param User $user
     * @param int $productId
     * @return array
     */
    public function toggleWishlistItem(User $user, int $productId): array
    {
        try {
            DB::beginTransaction();
            
            $wishlistItem = $this->getWishlistItem($user, $productId);
            
            if ($wishlistItem) {
                // Remove from wishlist
                $this->removeWishlistItem($user, $wishlistItem->id);
                
                DB::commit();
                
                return [
                    'success' => true,
                    'message' => 'Product removed from wishlist',
                    'in_wishlist' => false,
                    'status' => 200
                ];
            }
            
            // Add to wishlist
            $wishlistItem = $user->wishlist()->create([
                'product_id' => $productId
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Product added to wishlist',
                'in_wishlist' => true,
                'data' => $wishlistItem->load('product'),
                'status' => 201
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to toggle product in wishlist: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to update wishlist. Please try again.',
                'status' => 500
            ];
        }
    }

    /**
     * Update wishlist item
     *
     * @param User $user
     * @param int $wishlistId
     * @param array $data
     * @return array
     */
    public function updateWishlistItem(User $user, int $wishlistId, array $data): array
    {
        try {
            $wishlistItem = $this->getWishlistItemById($user, $wishlistId);
            
            if (!$wishlistItem) {
                return [
                    'success' => false,
                    'message' => 'Wishlist item not found.',
                    'status' => 404
                ];
            }
            
            DB::beginTransaction();
            
            $wishlistItem->update([
                'notes' => $data['notes'] ?? $wishlistItem->notes,
                'priority' => $data['priority'] ?? $wishlistItem->priority,
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Wishlist item updated successfully.',
                'data' => $wishlistItem->fresh()->load('product'),
                'status' => 200
            ];
            
        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Wishlist item not found.',
                'status' => 404
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update wishlist item: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to update wishlist item. Please try again.',
                'status' => 500
            ];
        }
    }

    /**
     * Remove item from wishlist
     *
     * @param User $user
     * @param int $wishlistId
     * @return array
     */
    public function removeWishlistItem(User $user, int $wishlistId): array
    {
        try {
            $wishlistItem = $this->getWishlistItemById($user, $wishlistId);
            
            if (!$wishlistItem) {
                return [
                    'success' => false,
                    'message' => 'Wishlist item not found.',
                    'status' => 404
                ];
            }
            
            DB::beginTransaction();
            
            $wishlistItem->delete();
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Item removed from wishlist.',
                'status' => 200
            ];
            
        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Wishlist item not found.',
                'status' => 404
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove wishlist item: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to remove item from wishlist. Please try again.',
                'status' => 500
            ];
        }
    }

    /**
     * Clear user's wishlist
     *
     * @param User $user
     * @return array
     */
    public function clearWishlist(User $user): array
    {
        try {
            DB::beginTransaction();
            
            $count = $user->wishlist()->delete();
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => "Successfully cleared {$count} items from your wishlist.",
                'count' => $count,
                'status' => 200
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to clear wishlist: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to clear wishlist. Please try again.',
                'status' => 500
            ];
        }
    }

    /**
     * Get wishlist item by ID
     *
     * @param User $user
     * @param int $wishlistId
     * @return Wishlist|null
     */
    public function getWishlistItemById(User $user, int $wishlistId): ?Wishlist
    {
        return $user->wishlist()
            ->with(['product.category', 'product.brand', 'product.media'])
            ->find($wishlistId);
    }

    /**
     * Get wishlist item by product ID
     *
     * @param User $user
     * @param int $productId
     * @return Wishlist|null
     */
    public function getWishlistItem(User $user, int $productId): ?Wishlist
    {
        return $user->wishlist()
            ->where('product_id', $productId)
            ->with(['product.category', 'product.brand', 'product.media'])
            ->first();
    }

    /**
     * Check if a product is in user's wishlist
     *
     * @param User $user
     * @param int $productId
     * @return bool
     */
    public function isInWishlist(User $user, int $productId): bool
    {
        return $user->wishlist()
            ->where('product_id', $productId)
            ->exists();
    }
}
