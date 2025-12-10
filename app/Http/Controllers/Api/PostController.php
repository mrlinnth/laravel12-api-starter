<?php

namespace App\Http\Controllers\Api;

use App\Data\PostData;
use App\Http\Resources\Api\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;

class PostController extends BaseApiController
{
    protected function model(): string
    {
        return Post::class;
    }

    protected function resource(): string
    {
        return PostResource::class;
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::exact('status'),
            AllowedFilter::exact('user_id'),
            'title',
            'content',
        ];
    }

    protected function allowedSorts(): array
    {
        return ['created_at', 'published_at', 'title', 'id'];
    }

    protected function allowedIncludes(): array
    {
        return ['user', 'comments', 'tags'];
    }

    public function store(PostData $data): JsonResponse
    {
        $post = Post::create($data->toArray());

        return $this->createdResponse(
            new PostResource($post),
            'Post created successfully'
        );
    }

    public function update(PostData $data, Post $post): JsonResponse
    {
        $post->update($data->toArray());

        return $this->successResponse(
            new PostResource($post),
            'Post updated successfully'
        );
    }

    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return $this->deletedResponse('Post deleted successfully');
    }
}
