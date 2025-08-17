<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $authService;
    protected $user;
    protected $password = 'password';

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->authService = new AuthService();
        $this->user = User::factory()->create([
            'password' => Hash::make($this->password)
        ]);
        
        // Start the session for the test
        $this->session(['_token' => Str::random(40)]);
    }

    /** @test */
    public function it_can_authenticate_user_with_valid_credentials()
    {
        $credentials = [
            'email' => $this->user->email,
            'password' => $this->password,
        ];
        
        $result = $this->authService->login($credentials);
        
        $this->assertTrue($result);
        $this->assertAuthenticatedAs($this->user);
    }

    /** @test */
    public function it_fails_to_authenticate_with_invalid_credentials()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        
        $this->authService->login([
            'email' => $this->user->email,
            'password' => 'wrong-password'
        ]);

        $this->assertGuest();
    }

    /** @test */
    public function it_can_logout_authenticated_user()
    {
        // First login the user
        $this->actingAs($this->user);
        $this->assertAuthenticatedAs($this->user);
        
        // Now test logout
        $result = $this->authService->logout();
        
        $this->assertTrue($result);
        $this->assertGuest();
    }

    /** @test */
    public function it_can_send_password_reset_link()
    {
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn(Password::RESET_LINK_SENT);

        $response = $this->authService->sendPasswordResetLink([
            'email' => $this->user->email
        ]);

        $this->assertEquals('We have emailed your password reset link!', $response);
    }

    /** @test */
    public function it_can_reset_password()
    {
        $token = Str::random(60);
        $newPassword = 'new-password123';
        
        Password::shouldReceive('reset')
            ->once()
            ->andReturn(Password::PASSWORD_RESET);

        $response = $this->authService->resetPassword([
            'email' => $this->user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
            'token' => $token
        ]);

        $this->assertEquals('Your password has been reset!', $response);
    }

    /** @test */
    public function it_can_update_user_password()
    {
        $newPassword = 'new-secure-password';
        
        $result = $this->authService->updatePassword(
            $this->user,
            $this->password,
            $newPassword
        );

        $this->assertTrue($result);
        $this->assertTrue(Hash::check($newPassword, $this->user->fresh()->password));
    }

    /** @test */
    public function it_fails_to_update_password_with_incorrect_current_password()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        
        $this->authService->updatePassword(
            $this->user,
            'wrong-current-password',
            'new-password'
        );
    }

    /** @test */
    public function it_can_get_authenticated_user()
    {
        Auth::login($this->user);
        
        $authenticatedUser = $this->authService->getAuthenticatedUser();
        
        $this->assertInstanceOf(User::class, $authenticatedUser);
        $this->assertEquals($this->user->id, $authenticatedUser->id);
    }

    /** @test */
    public function it_can_check_if_user_is_authenticated()
    {
        $this->assertFalse($this->authService->check());
        
        Auth::login($this->user);
        
        $this->assertTrue($this->authService->check());
    }
}
