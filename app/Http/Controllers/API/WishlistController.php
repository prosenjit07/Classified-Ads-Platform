<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\WishlistRequest;
use App\Http\Resources\WishlistResource;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WishlistController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('throttle:60,1');
    }

    /**
     * Get the authenticated user's wishlist.
     */
    /**
     * Get the authenticated user's wishlist.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $wishlistItems = $user->wishlist()
                ->with(['product.category', 'product.brand', 'product.media'])
                ->paginate(12);

            return response()->json([
                'success' => true,
                'data' => WishlistResource::collection($wishlistItems),
                'meta' => [
                    'total' => $wishlistItems->total(),
                    'per_page' => $wishlistItems->perPage(),
                    'current_page' => $wishlistItems->currentPage(),
                    'last_page' => $wishlistItems->lastPage(),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch wishlist: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wishlist. Please try again.',
            ], 500);
        }
    }

    /**
     * Add a product to the user's wishlist.
     */
    public function store(WishlistRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        try {
            DB::beginTransaction();
            
            // Check if the product is already in the user's wishlist
            $existingWishlistItem = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $validated['product_id'])
                ->first();
                
            if ($existingWishlistItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product is already in your wishlist.',
                    'data' => new WishlistResource($existingWishlistItem->load('product')),
                ], 409);
            }
            
            // Create new wishlist item
            $wishlistItem = Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $validated['product_id'],
                'notes' => $validated['notes'] ?? null,
                'priority' => $validated['priority'] ?? 0,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist successfully.',
                'data' => new WishlistResource($wishlistItem->load('product')),
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add product to wishlist: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to wishlist. Please try again.',
            ], 500);
        }
    }

    /**
     * Toggle a product in the user's wishlist.
     * 
     * @param Product $product The product to toggle in the wishlist
     * @return JsonResponse
     */
    public function toggle(Product $product): JsonResponse
    {
        $user = Auth::user();
        
        try {
            DB::beginTransaction();
            
            // Check if product is already in wishlist
            $wishlistItem = Wishlist::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->first();
                
            if ($wishlistItem) {
                // Remove from wishlist
                $wishlistItem->delete();
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Product removed from wishlist',
                    'in_wishlist' => false,
                ]);
            }
            
            // Add to wishlist
            $wishlistItem = Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist',
                'in_wishlist' => true,
                'data' => new WishlistResource($wishlistItem->load('product')),
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to toggle product in wishlist: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update wishlist. Please try again.',
            ], 500);
        }
    }

    /**
     * Show the specified wishlist item.
     */
    public function show(Wishlist $wishlist): JsonResponse
    {
        try {
            // Ensure the user can only view their own wishlist items
            if ($wishlist->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to wishlist item.',
                ], 403);
            }
            
            return response()->json([
                'success' => true,
                'data' => new WishlistResource($wishlist->load('product')),
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Wishlist item not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to fetch wishlist item: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wishlist item. Please try again.',
            ], 500);
        }
    }
    
    /**
     * Update the specified wishlist item.
     */
    public function update(WishlistRequest $request, Wishlist $wishlist): JsonResponse
    {
        try {
            // Ensure the user can only update their own wishlist items
            if ($wishlist->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this wishlist item.',
                ], 403);
            }
            
            $validated = $request->validated();
            
            DB::beginTransaction();
            
            $wishlist->update([
                'notes' => $validated['notes'] ?? $wishlist->notes,
                'priority' => $validated['priority'] ?? $wishlist->priority,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Wishlist item updated successfully.',
                'data' => new WishlistResource($wishlist->fresh()->load('product')),
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Wishlist item not found.',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update wishlist item: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update wishlist item. Please try again.',
            ], 500);
        }
    }
    
    /**
     * Remove the specified wishlist item.
     */
    public function destroy(Wishlist $wishlist): JsonResponse
    {
        try {
            // Ensure the user can only delete their own wishlist items
            if ($wishlist->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this wishlist item.',
                ], 403);
            }
            
            DB::beginTransaction();
            
            $wishlist->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Item removed from wishlist.',
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Wishlist item not found.',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove wishlist item: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from wishlist. Please try again.',
            ], 500);
        }
    }
    
    /**
     * Clear all items from the user's wishlist.
     */
    /**
     * Clear all items from the user's wishlist.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear(): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            /** @var User $user */
            $user = Auth::user();
            $count = $user->wishlist()->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully cleared {$count} items from your wishlist.",
                'count' => $count,
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to clear wishlist: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear wishlist. Please try again.',
            ], 500);
        }
    }
}
