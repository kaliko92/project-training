<?php

namespace Tests\Feature;

use App\Events\PostCreated;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

class PostBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_created_event_is_broadcasted()
    {
        Event::fake(); 

        $user = User::factory()->create();
        $postData = [
            'title'   => 'Test Post',
            'content' => 'This is a test post.',
            'user_id' => $user->id,
        ];

        // Create a post
        $this->actingAs($user)
            ->postJson('http://localhost:8000/api/v1/posts', $postData)
            ->assertStatus(201);

        // Assert that the event was broadcasted
        Event::assertDispatched(PostCreated::class, function ($event) use ($postData) {
            return $event->post->title === $postData['title'];
        });
    }
}
