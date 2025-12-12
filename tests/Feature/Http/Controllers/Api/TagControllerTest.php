<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\TagController
 */
final class TagControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $tags = Tag::factory()->count(3)->create();

        $response = $this->get(route('tags.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\TagController::class,
            'store',
            \App\Http\Requests\Api\TagStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $title = fake()->sentence(4);

        $response = $this->post(route('tags.store'), [
            'title' => $title,
        ]);

        $tags = Tag::query()
            ->where('title', $title)
            ->get();
        $this->assertCount(1, $tags);
        $tag = $tags->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->get(route('tags.show', $tag));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\TagController::class,
            'update',
            \App\Http\Requests\Api\TagUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $tag = Tag::factory()->create();
        $title = fake()->sentence(4);

        $response = $this->put(route('tags.update', $tag), [
            'title' => $title,
        ]);

        $tag->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($title, $tag->title);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->delete(route('tags.destroy', $tag));

        $response->assertNoContent();

        $this->assertModelMissing($tag);
    }
}
