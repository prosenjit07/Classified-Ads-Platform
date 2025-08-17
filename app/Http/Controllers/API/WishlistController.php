<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\WishlistRequest;
use App\Http\Resources\WishlistResource;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\User;
use App\Services\WishlistService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WishlistController extends Controller
{
    /**
     * @var WishlistService
     */
    protected $wishlistService;

    /**
     * Create a new controller instance.
     *
     * @param WishlistService $wishlistService
     * @return void
     */
    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
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
        /** @var User $user */
        $user = Auth::user();
        $wishlistItems = $this->wishlistService->getUserWishlist($user);

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
    }

    /**
     * Add a product to the user's wishlist.
     */
    public function store(WishlistRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = Auth::user();
        
        $result = $this->wishlistService->addToWishlist($user, $validated);
        
        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
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
        $result = $this->wishlistService->toggleWishlistItem($user, $product->id);
        
        $response = [
            'success' => $result['success'],
            'message' => $result['message'],
            'in_wishlist' => $result['in_wishlist'] ?? false,
        ];
        
        if (isset($result['data'])) {
            $response['data'] = new WishlistResource($result['data']);
        }
        
        return response()->json($response, $result['status']);
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
        // Ensure the user can only update their own wishlist items
        if ($wishlist->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this wishlist item.',
            ], 403);
        }
        
        $validated = $request->validated();
        $user = Auth::user();
        
        $result = $this->wishlistService->updateWishlistItem($user, $wishlist->id, $validated);
        
        $response = [
            'success' => $result['success'],
            'message' => $result['message'],
        ];
        
        if (isset($result['data'])) {
            $response['data'] = new WishlistResource($result['data']);
        }
        
        return response()->json($response, $result['status']);
    }
    
    /**
     * Remove the specified wishlist item.
     */
    public function destroy(Wishlist $wishlist): JsonResponse
    {
        // Ensure the user can only delete their own wishlist items
        if ($wishlist->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this wishlist item.',
            ], 403);
        }
        
        $user = Auth::user();
        $result = $this->wishlistService->removeWishlistItem($user, $wishlist->id);
        
        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ], $result['status']);
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
        /** @var User $user */
        $user = Auth::user();
        $result = $this->wishlistService->clearWishlist($user);
        
        return response()->json(
            array_merge(
                ['success' => $result['success'], 'message' => $result['message']],
            isset($result['count']) ? ['count' => $result['count']] : []
        ), $result['status']);
    }
}
