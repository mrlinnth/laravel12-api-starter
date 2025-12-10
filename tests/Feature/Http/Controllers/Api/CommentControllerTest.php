<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\CommentController
 */
final class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_returns_paginated_comments(): void
    {
        Comment::factory()->count(3)->create();

        $response = $this->getJson(route('comments.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'content'],
            ],
        ]);
    }

    #[Test]
    public function show_returns_single_comment(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->getJson(route('comments.show', $comment));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data' => ['id', 'content'],
        ]);
    }

    #[Test]
    public function store_creates_comment_with_valid_data(): void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();

        $data = [
            'content' => 'Test comment',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ];

        $response = $this->postJson(route('comments.store'), $data);

        $response->assertCreated();
        $this->assertDatabaseHas('comments', [
            'content' => 'Test comment',
            'post_id' => $post->id,
        ]);
    }

    #[Test]
    public function store_validates_required_fields(): void
    {
        $response = $this->postJson(route('comments.store'), []);

        $response->assertUnprocessable();
    }

    #[Test]
    public function update_modifies_comment_with_valid_data(): void
    {
        $comment = Comment::factory()->create();
        $post = Post::factory()->create();
        $user = User::factory()->create();

        $data = [
            'content' => 'Updated comment',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ];

        $response = $this->putJson(route('comments.update', $comment), $data);

        $response->assertOk();
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated comment',
        ]);
    }

    #[Test]
    public function destroy_deletes_comment(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->deleteJson(route('comments.destroy', $comment));

        $response->assertOk();
        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }
}
