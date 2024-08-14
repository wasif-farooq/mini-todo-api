<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService;
    }

    /**
     * Test user registration.
     *
     * @return void
     */
    public function testRegister()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
        ];

        $result = $this->authService->register($data);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);

        $this->assertInstanceOf(User::class, $result['user']);
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
        $this->assertNotEmpty($result['token']);
    }

    /**
     * Test user login with valid credentials.
     *
     * @return void
     */
    public function testLoginWithValidCredentials()
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);

        $credentials = [
            'email' => 'testuser@example.com',
            'password' => 'password',
        ];

        $result = $this->authService->login($credentials);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);

        $this->assertInstanceOf(User::class, $result['user']);
        $this->assertEquals($user->id, $result['user']->id);
        $this->assertNotEmpty($result['token']);
    }

    /**
     * Test user login with invalid credentials.
     *
     * @return void
     */
    public function testLoginWithInvalidCredentials()
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);

        $credentials = [
            'email' => 'testuser@example.com',
            'password' => 'wrong_password',
        ];

        $this->authService->login($credentials);
    }

    /**
     * Test user logout.
     *
     * @return void
     */
    public function testLogout()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->authService->logout();

        $this->assertNull(Auth::user());
    }

    /**
     * Test fetching the currently authenticated user.
     *
     * @return void
     */
    public function testMe()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $authenticatedUser = $this->authService->me();

        $this->assertInstanceOf(User::class, $authenticatedUser);
        $this->assertEquals($user->id, $authenticatedUser->id);
    }
}
