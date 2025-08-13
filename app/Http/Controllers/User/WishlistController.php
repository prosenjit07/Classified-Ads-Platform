<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlist.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        $wishlistItems = Wishlist::where('user_id', $user->id)
            ->with(['product.category', 'product.brand', 'product.media'])
            ->latest()
            ->paginate(12);
            
        return Inertia::render('User/Wishlist/Index', [
            'wishlistItems' => $wishlistItems
        ]);
    }
}
