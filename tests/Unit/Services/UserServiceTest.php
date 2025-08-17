<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Product;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $userService;
    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->userService = app(UserService::class);
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create();
    }

    /** @test */
    public function it_can_create_a_new_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $user = $this->userService->createUser($userData);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /** @test */
    public function it_can_send_welcome_email()
    {
        Mail::fake();
        
        $this->userService->sendWelcomeEmail($this->user);
        
        Mail::assertSent(WelcomeMail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    /** @test */
    public function it_handles_welcome_email_failure_gracefully()
    {
        Mail::shouldReceive('to')
            ->once()
            ->andThrow(new \Exception('Mail server error'));
            
        $this->userService->sendWelcomeEmail($this->user);
        
        // No exception should be thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_update_user_profile()
    {
        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ];

        $updatedUser = $this->userService->updateProfile($this->user, $updatedData);

        $this->assertEquals('Updated Name', $updatedUser->name);
        $this->assertEquals('updated@example.com', $updatedUser->email);
    }

    /** @test */
    public function it_can_update_user_password()
    {
        $newPassword = 'new-secure-password';
        
        $this->userService->updateProfile($this->user, [
            'password' => $newPassword
        ]);

        $this->assertTrue(Hash::check($newPassword, $this->user->fresh()->password));
    }

    /** @test */
    public function it_can_retrieve_user_wishlist()
    {
        // Add items to wishlist
        $products = Product::factory()->count(3)->create();
        foreach ($products as $product) {
            $this->user->wishlist()->create([
                'product_id' => $product->id
            ]);
        }

        $wishlist = $this->userService->getWishlist($this->user);
        
        $this->assertCount(3, $wishlist);
        $this->assertArrayHasKey('formatted_price', $wishlist[0]);
    }

    /** @test */
    public function it_can_add_product_to_wishlist()
    {
        $result = $this->userService->addToWishlist(
            $this->user, 
            $this->product->id,
            'Test notes',
            1
        );

        $this->assertEquals('added', $result['action']);
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'notes' => 'Test notes',
            'priority' => 1
        ]);
    }

    /** @test */
    public function it_can_remove_product_from_wishlist()
    {
        // First add to wishlist
        $this->user->wishlist()->create([
            'product_id' => $this->product->id
        ]);

        // Then remove
        $result = $this->userService->removeFromWishlist(
            $this->user,
            $this->product->id
        );

        $this->assertTrue($result);
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);
    }

    /** @test */
    public function it_can_toggle_wishlist_item()
    {
        // Add to wishlist
        $result = $this->userService->addToWishlist($this->user, $this->product->id);
        $this->assertEquals('added', $result['action']);

        // Remove from wishlist
        $result = $this->userService->addToWishlist($this->user, $this->product->id);
        $this->assertEquals('removed', $result['action']);
    }
}
