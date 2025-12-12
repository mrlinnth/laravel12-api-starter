<?php

namespace App\Http\Controllers\Api;

use App\Data\PostData;
use App\Http\Resources\Api\PostResource;
use App\Models\Post;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\PathParameter;
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

    /**
     * Store a newly created post.
     *
     * @response array{success: bool, message: string, data: array{id: int, title: string, content: string, status: string, user_id: int, published_at: string|null, created_at: string, updated_at: string}}
     */
    #[BodyParameter('title', description: 'The title of the post.', type: 'string', required: true, example: 'My First Blog Post')]
    #[BodyParameter('content', description: 'The content of the post.', type: 'string', required: true, example: 'This is the content of my blog post...')]
    #[BodyParameter('status', description: 'The status of the post.', type: 'string', required: true, example: 'draft')]
    #[BodyParameter('user_id', description: 'The ID of the user creating the post.', type: 'integer', required: true, example: 1)]
    #[BodyParameter('published_at', description: 'The date and time when the post was published.', type: 'string', format: 'date-time', required: false, example: '2025-12-11T10:00:00Z')]
    public function store(PostData $data): JsonResponse
    {
        $post = Post::create($data->toArray());

        return $this->createdResponse(
            new PostResource($post),
            'Post created successfully'
        );
    }

    /**
     * Update the specified post.
     *
     * @response array{success: bool, message: string, data: array{id: int, title: string, content: string, status: string, user_id: int, published_at: string|null, created_at: string, updated_at: string}}
     */
    #[PathParameter('post', description: 'The post to update.', type: 'integer', example: 1)]
    #[BodyParameter('title', description: 'The title of the post.', type: 'string', required: true, example: 'Updated Blog Post Title')]
    #[BodyParameter('content', description: 'The content of the post.', type: 'string', required: true, example: 'This is the updated content...')]
    #[BodyParameter('status', description: 'The status of the post.', type: 'string', required: true, example: 'published')]
    #[BodyParameter('user_id', description: 'The ID of the user who owns the post.', type: 'integer', required: true, example: 1)]
    #[BodyParameter('published_at', description: 'The date and time when the post was published.', type: 'string', format: 'date-time', required: false, example: '2025-12-11T10:00:00Z')]
    public function update(PostData $data, Post $post): JsonResponse
    {
        $post->update($data->toArray());

        return $this->successResponse(
            new PostResource($post),
            'Post updated successfully'
        );
    }

    /**
     * Remove the specified post.
     *
     * @response array{success: bool, message: string}
     */
    #[PathParameter('post', description: 'The post to delete.', type: 'integer', example: 1)]
    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return $this->deletedResponse('Post deleted successfully');
    }
}
