<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use App\Services\WishlistService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class WishlistServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $wishlistService;
    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->wishlistService = app(WishlistService::class);
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create();
    }

    /** @test */
    public function it_can_add_product_to_wishlist()
    {
        $result = $this->wishlistService->addToWishlist($this->user, [
            'product_id' => $this->product->id,
            'notes' => 'Test notes',
            'priority' => 1
        ]);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'notes' => 'Test notes',
            'priority' => 1
        ]);
    }

    /** @test */
    public function it_cannot_add_duplicate_product_to_wishlist()
    {
        // First add
        $this->wishlistService->addToWishlist($this->user, [
            'product_id' => $this->product->id
        ]);

        // Try to add again
        $result = $this->wishlistService->addToWishlist($this->user, [
            'product_id' => $this->product->id
        ]);

        $this->assertFalse($result['success']);
        $this->assertEquals(409, $result['status']);
    }

    /** @test */
    public function it_can_toggle_wishlist_item()
    {
        // Add to wishlist
        $result = $this->wishlistService->toggleWishlistItem($this->user, $this->product->id);
        $this->assertTrue($result['in_wishlist']);
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);

        // Remove from wishlist
        $result = $this->wishlistService->toggleWishlistItem($this->user, $this->product->id);
        $this->assertFalse($result['in_wishlist']);
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);
    }

    /** @test */
    public function it_can_retrieve_user_wishlist()
    {
        // Add some items to wishlist
        $products = Product::factory()->count(3)->create();
        foreach ($products as $product) {
            $this->wishlistService->addToWishlist($this->user, [
                'product_id' => $product->id
            ]);
        }

        $wishlist = $this->wishlistService->getUserWishlist($this->user, 2);
        
        $this->assertInstanceOf(LengthAwarePaginator::class, $wishlist);
        $this->assertEquals(3, $wishlist->total());
        $this->assertEquals(2, $wishlist->perPage());
    }

    /** @test */
    public function it_can_update_wishlist_item()
    {
        // First add an item
        $wishlistItem = $this->wishlistService->addToWishlist($this->user, [
            'product_id' => $this->product->id,
            'notes' => 'Old notes',
            'priority' => 1
        ]);

        // Update the item
        $result = $this->wishlistService->updateWishlistItem($this->user, $wishlistItem['data']->id, [
            'notes' => 'Updated notes',
            'priority' => 2
        ]);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('wishlists', [
            'id' => $wishlistItem['data']->id,
            'user_id' => $this->user->id,
            'notes' => 'Updated notes',
            'priority' => 2
        ]);
    }

    /** @test */
    public function it_can_remove_wishlist_item()
    {
        // First add an item
        $wishlistItem = $this->wishlistService->addToWishlist($this->user, [
            'product_id' => $this->product->id
        ]);

        // Remove the item
        $result = $this->wishlistService->removeWishlistItem($this->user, $wishlistItem['data']->id);

        $this->assertTrue($result['success']);
        $this->assertDatabaseMissing('wishlists', [
            'id' => $wishlistItem['data']->id,
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_can_clear_wishlist()
    {
        // Add some items to wishlist
        $products = Product::factory()->count(3)->create();
        foreach ($products as $product) {
            $this->wishlistService->addToWishlist($this->user, [
                'product_id' => $product->id
            ]);
        }

        $result = $this->wishlistService->clearWishlist($this->user);

        $this->assertTrue($result['success']);
        $this->assertEquals(3, $result['count']);
        $this->assertEquals(0, $this->user->wishlist()->count());
    }

    /** @test */
    public function it_can_check_if_product_is_in_wishlist()
    {
        // Initially not in wishlist
        $this->assertFalse($this->wishlistService->isInWishlist($this->user, $this->product->id));

        // Add to wishlist
        $this->wishlistService->addToWishlist($this->user, [
            'product_id' => $this->product->id
        ]);

        // Now should be in wishlist
        $this->assertTrue($this->wishlistService->isInWishlist($this->user, $this->product->id));
    }
}
