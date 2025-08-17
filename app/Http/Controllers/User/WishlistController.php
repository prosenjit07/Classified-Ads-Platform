<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\WishlistService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;

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
     */
    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    /**
     * Display the user's wishlist.
     *
     * @return Response
     */
    public function index(): Response
    {
        $user = Auth::user();
        
        $wishlistItems = $this->wishlistService->getUserWishlist($user);
            
        return Inertia::render('User/Wishlist/Index', [
            'wishlistItems' => $wishlistItems
        ]);
    }
}
