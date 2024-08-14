<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration.
     *
     * @return void
     */
    public function testRegister()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id', 'name', 'email', 'created_at', 'updated_at',
                ],
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    /**
     * Test user login.
     *
     * @return void
     */
    public function testLogin()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'testuser@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id', 'name', 'email', 'created_at', 'updated_at',
                ],
                'token',
            ]);
    }

    /**
     * Test fetching the authenticated user's details.
     *
     * @return void
     */
    public function testMe()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user, 'api')->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => 'Test User',
                'email' => 'testuser@example.com',
            ]);
    }

    /**
     * Test user logout.
     *
     * @return void
     */
    public function testLogout()
    {
        // Create a user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);

        // Simulate login to get the JWT token
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'testuser@example.com',
            'password' => 'password',
        ]);

        // Extract the token from the login response
        $token = $loginResponse->json('token');

        // Now logout using the token
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/logout');

        // Assert the response is as expected
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
            ]);
    }
}
