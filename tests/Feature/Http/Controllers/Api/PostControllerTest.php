<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\PostController
 */
final class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_returns_paginated_posts(): void
    {
        Post::factory()->count(3)->create();

        $response = $this->getJson(route('posts.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'title', 'content', 'status'],
            ],
        ]);
    }

    #[Test]
    public function show_returns_single_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->getJson(route('posts.show', $post));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data' => ['id', 'title', 'content', 'status'],
        ]);
    }

    #[Test]
    public function store_creates_post_with_valid_data(): void
    {
        $user = User::factory()->create();

        $data = [
            'title' => 'Test Post',
            'content' => 'Test content',
            'status' => 'draft',
            'published_at' => null,
            'user_id' => $user->id,
        ];

        $response = $this->postJson(route('posts.store'), $data);

        $response->assertCreated();
        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'content' => 'Test content',
            'status' => 'draft',
        ]);
    }

    #[Test]
    public function store_validates_required_fields(): void
    {
        $response = $this->postJson(route('posts.store'), []);

        $response->assertUnprocessable();
    }

    #[Test]
    public function update_modifies_post_with_valid_data(): void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();

        $data = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'status' => 'published',
            'published_at' => now()->toISOString(),
            'user_id' => $user->id,
        ];

        $response = $this->putJson(route('posts.update', $post), $data);

        $response->assertOk();
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ]);
    }

    #[Test]
    public function destroy_deletes_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->deleteJson(route('posts.destroy', $post));

        $response->assertOk();
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }
}
