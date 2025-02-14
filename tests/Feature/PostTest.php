<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and generate a token for authenticated requests
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('auth_token')->plainTextToken;
    }

    // Test fetching all posts
    public function test_fetch_all_posts()
    {
        Post::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('http://localhost:8000/api/v1/posts');

        $response->assertStatus(200)
                    ->assertJsonStructure([
                        'data' => [
                            '*' => ['id', 'title', 'content'],
                        ],
                    ]);
    }

    // Test creating a post
    public function test_create_post()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('http://localhost:8000/api/v1/posts', [
            'title' => 'Test Post',
            'content' => 'This is a test post.',
        ]);

        $response->assertStatus(201)
                    ->assertJson([
                        'data' => [
                            'title' => 'Test Post',
                            'content' => 'This is a test post.',
                        ],
                    ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'content' => 'This is a test post.',
        ]);
    }

    // Test fetching a single post
    public function test_fetch_single_post()
    {
        $post = Post::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('http://localhost:8000/api/v1/posts/' . $post->id);

        $response->assertStatus(200)
                    ->assertJson([
                        'data' => [
                            'id' => $post->id,
                            'title' => $post->title,
                            'content' => $post->content,
                        ],
                    ]);
    }

    // Test updating a post
    public function test_update_post()
    {
        $post = Post::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('http://localhost:8000/api/v1/posts/' . $post->id, [
            'title' => 'Updated Title',
            'content' => 'Updated content.',
        ]);

        $response->assertStatus(200)
                    ->assertJson([
                        'data' => [
                            'title' => 'Updated Title',
                            'content' => 'Updated content.',
                        ],
                    ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'content' => 'Updated content.',
        ]);
    }

    // Test deleting a post
    public function test_delete_post()
    {
        $post = Post::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('http://localhost:8000/api/v1/posts/' . $post->id);

        $response->assertStatus(200)
                    ->assertJson([
                        'message' => 'Post deleted successfully',
                    ]);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_caching_all_posts()
    {
        // Fetch posts (should cache the response)
        $response1 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('http://localhost:8000/api/v1/posts');

        $response1->assertStatus(200);

        // Fetch posts again (should return cached response)
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('http://localhost:8000/api/v1/posts');

        $response2->assertStatus(200);

        // Verify the responses are identical
        $this->assertEquals($response1->json(), $response2->json());
    }

    public function test_caching_single_post()
    {
        $post = Post::factory()->create();

        // Fetch the post (should cache the response)
        $response1 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('http://localhost:8000/api/v1/posts/' . $post->id);

        $response1->assertStatus(200);

        // Fetch the post again (should return cached response)
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('http://localhost:8000/api/v1/posts/' . $post->id);

        $response2->assertStatus(200);

        // Verify the responses are identical
        $this->assertEquals($response1->json(), $response2->json());
    }

    public function test_cache_cleared_after_post_creation()
    {
        // Fetch posts (should cache the response)
        $response1 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('http://localhost:8000/api/v1/posts');

        $response1->assertStatus(200);

        // Create a new post (should clear the cache)
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('http://localhost:8000/api/v1/posts', [
            'title' => 'New Post',
            'content' => 'This is a new post.',
        ]);

        // Fetch posts again (should not return cached response)
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('http://localhost:8000/api/v1/posts');

        $response2->assertStatus(200);

        // Verify the responses are not identical
        $this->assertNotEquals($response1->json(), $response2->json());
    }


    public function test_v1_fetch_all_posts()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('http://localhost:8000/api/v1/posts');

        $response->assertStatus(200);
    }

    public function test_v2_fetch_all_posts()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('http://localhost:8000/api/v2/posts');

        $response->assertStatus(200);
    }

}