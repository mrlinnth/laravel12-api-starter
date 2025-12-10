<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TagStoreRequest;
use App\Http\Requests\Api\TagUpdateRequest;
use App\Http\Resources\Api\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function store(TagStoreRequest $request): JsonResponse
    {
        $tag = Tag::create($request->validated());

        return $this->createdResponse(
            new TagResource($tag),
            'Tag created successfully'
        );
    }

    public function update(TagUpdateRequest $request, Tag $tag): JsonResponse
    {
        $tag->update($request->validated());

        return $this->successResponse(
            new TagResource($tag),
            'Tag updated successfully'
        );
    }

    public function destroy(Request $request, Tag $tag): JsonResponse
    {
        $tag->delete();

        return $this->deletedResponse('Tag deleted successfully');
    }
}
