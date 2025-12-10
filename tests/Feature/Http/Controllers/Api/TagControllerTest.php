<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\TagController
 */
final class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_returns_paginated_tags(): void
    {
        Tag::factory()->count(3)->create();

        $response = $this->getJson(route('tags.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'title'],
            ],
        ]);
    }

    #[Test]
    public function show_returns_single_tag(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->getJson(route('tags.show', $tag));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data' => ['id', 'title'],
        ]);
    }

    #[Test]
    public function store_creates_tag_with_valid_data(): void
    {
        $data = [
            'title' => 'Laravel',
        ];

        $response = $this->postJson(route('tags.store'), $data);

        $response->assertCreated();
        $this->assertDatabaseHas('tags', [
            'title' => 'Laravel',
        ]);
    }

    #[Test]
    public function store_validates_required_fields(): void
    {
        $response = $this->postJson(route('tags.store'), []);

        $response->assertUnprocessable();
    }

    #[Test]
    public function update_modifies_tag_with_valid_data(): void
    {
        $tag = Tag::factory()->create();

        $data = [
            'title' => 'Updated Tag',
        ];

        $response = $this->putJson(route('tags.update', $tag), $data);

        $response->assertOk();
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'title' => 'Updated Tag',
        ]);
    }

    #[Test]
    public function destroy_deletes_tag(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->deleteJson(route('tags.destroy', $tag));

        $response->assertOk();
        $this->assertSoftDeleted('tags', ['id' => $tag->id]);
    }
}
