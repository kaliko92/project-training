<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    // Test user registration
    public function test_user_registration()
    {
        $response = $this->postJson('http://localhost:8000/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
                    ->assertJson([
                        'success' => true,
                        'message' => 'User registered successfully.',
                    ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    // Test user login
    public function test_user_login()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('http://localhost:8000/api/v1/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                    ->assertJson([
                        'success' => true,
                        'message' => 'User logged in successfully.',
                    ])
                    ->assertJsonStructure([
                        'data' => [
                            'user' => ['id', 'name', 'email'],
                            'token',
                        ],
                    ]);
    }

    // Test user logout
    public function test_user_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('http://localhost:8000/api/v1/logout');

        $response->assertStatus(200)
                    ->assertJson([
                        'success' => true,
                        'message' => 'User logged out successfully.',
                    ]);
    }
    


    public function test_rate_limiting_for_login()
    {
        $user = User::factory()->create();

        // Attempt to login more than 10 times in a minute
        for ($i = 0; $i < 11; $i++) {
            $response = $this->postJson('http://localhost:8000/api/v1/auth/login', [
                'email' => $user->email,
                'password' => '123456789',
            ]);
        }

        // The 11th request should be rate-limited
        $response->assertStatus(429); // 429 Too Many Requests
    }
}
