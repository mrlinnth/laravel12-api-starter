<?php

namespace App\Http\Controllers\Api;

use App\Data\TagData;
use App\Http\Resources\Api\TagResource;
use App\Models\Tag;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\PathParameter;
use Illuminate\Http\JsonResponse;

class TagController extends BaseApiController
{
    protected function model(): string
    {
        return Tag::class;
    }

    protected function resource(): string
    {
        return TagResource::class;
    }

    protected function allowedFilters(): array
    {
        return ['title'];
    }

    protected function allowedSorts(): array
    {
        return ['created_at', 'title', 'id'];
    }

    protected function allowedIncludes(): array
    {
        return ['posts'];
    }

    /**
     * Store a newly created tag.
     *
     * @response array{success: bool, message: string, data: array{id: int, title: string, created_at: string, updated_at: string}}
     */
    #[BodyParameter('title', description: 'The title of the tag. Must be unique.', type: 'string', required: true, example: 'Laravel')]
    public function store(TagData $data): JsonResponse
    {
        $tag = Tag::create($data->toArray());

        return $this->createdResponse(
            new TagResource($tag),
            'Tag created successfully'
        );
    }

    /**
     * Update the specified tag.
     *
     * @response array{success: bool, message: string, data: array{id: int, title: string, created_at: string, updated_at: string}}
     */
    #[PathParameter('tag', description: 'The tag to update.', type: 'integer', example: 1)]
    #[BodyParameter('title', description: 'The title of the tag. Must be unique.', type: 'string', required: true, example: 'PHP')]
    public function update(TagData $data, Tag $tag): JsonResponse
    {
        $tag->update($data->toArray());

        return $this->successResponse(
            new TagResource($tag),
            'Tag updated successfully'
        );
    }

    /**
     * Remove the specified tag.
     *
     * @response array{success: bool, message: string}
     */
    #[PathParameter('tag', description: 'The tag to delete.', type: 'integer', example: 1)]
    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();

        return $this->deletedResponse('Tag deleted successfully');
    }
}
