<?php

namespace App\Http\Controllers\Api;

use App\Data\TagData;
use App\Http\Resources\Api\TagResource;
use App\Models\Tag;
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

    public function store(TagData $data): JsonResponse
    {
        $tag = Tag::create($data->toArray());

        return $this->createdResponse(
            new TagResource($tag),
            'Tag created successfully'
        );
    }

    public function update(TagData $data, Tag $tag): JsonResponse
    {
        $tag->update($data->toArray());

        return $this->successResponse(
            new TagResource($tag),
            'Tag updated successfully'
        );
    }

    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();

        return $this->deletedResponse('Tag deleted successfully');
    }
}
