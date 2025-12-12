<?php

namespace App\Http\Controllers\Api;

use App\Data\PostData;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\PostResource;
use App\Models\Post;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            'title',
            AllowedFilter::exact('status'),
            AllowedFilter::exact('user_id'),
        ];
    }

    protected function allowedSorts(): array
    {
        return [
            'title',
            'published_at',
            'id',
        ];
    }

    protected function allowedIncludes(): array
    {
        return [
            'comment',
            'tag',
            'user',
        ];
    }

    /**
     * Display a paginated listing of posts.
     *
     * @response array{success: bool, data: PostResource[], links: array{first: string|null, last: string|null, prev: string|null, next: string|null}, meta: array{current_page: int, from: int|null, last_page: int, path: string, per_page: int, to: int|null, total: int}}
     */
    #[QueryParameter('filter', description: 'Filter results by field values. Use filter[field]=value format. Available fields: title, status, user_id', type: 'object', required: false, example: ['user_id' => 1])]
    #[QueryParameter('sort', description: 'Sort results by field. Prefix with - for descending order. Available fields: title, published_at, id', type: 'string', required: false, example: '-created_at')]
    #[QueryParameter('include', description: 'Include related resources. Comma-separated list. Available relationships: comment, tag, user', type: 'string', required: false, example: 'comment,tag')]
    #[QueryParameter('per_page', description: 'Number of items per page for pagination.', type: 'integer', required: false, default: 15, example: 20)]
    #[QueryParameter('page', description: 'Page number for pagination.', type: 'integer', required: false, default: 1, example: 1)]
    public function index(Request $request): JsonResponse
    {
        return parent::index($request);
    }

    /**
     * Display the specified post.
     *
     * @response array{success: bool, data: PostResource}
     */
    #[PathParameter('id', description: 'The ID of the post to retrieve.', type: 'integer', example: 1)]
    #[QueryParameter('include', description: 'Include related resources. Comma-separated list. Available relationships: comment, tag, user', type: 'string', required: false, example: 'comment,tag')]
    public function show(Request $request, $id): JsonResponse
    {
        return parent::show($request, $id);
    }

    /**
     * Store a newly created post.
     *
     * @status 201
     *
     * @response array{success: bool, message: string, data: PostResource}
     */
    #[BodyParameter('title', description: 'The Title.', type: 'string', required: true, example: 'Example Title')]
    #[BodyParameter('content', description: 'The Content.', type: 'string', required: true, example: 'This is example content for the article...')]
    #[BodyParameter('status', description: 'The Status.', type: 'string', required: true, example: 'draft')]
    #[BodyParameter('user_id', description: 'The ID of the User.', type: 'integer', required: true, example: 1)]
    #[BodyParameter('published_at', description: 'The Published At.', type: 'string', format: 'date-time', required: false, example: '2025-12-12T05:32:50.329264Z')]

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
     * @response array{success: bool, message: string, data: PostResource}
     */
    #[PathParameter('post', description: 'The post to update.', type: 'integer', example: 1)]
    #[BodyParameter('title', description: 'The Title.', type: 'string', required: true, example: 'Example Title')]
    #[BodyParameter('content', description: 'The Content.', type: 'string', required: true, example: 'This is example content for the article...')]
    #[BodyParameter('status', description: 'The Status.', type: 'string', required: true, example: 'draft')]
    #[BodyParameter('user_id', description: 'The ID of the User.', type: 'integer', required: true, example: 1)]
    #[BodyParameter('published_at', description: 'The Published At.', type: 'string', format: 'date-time', required: false, example: '2025-12-12T05:32:50.329762Z')]

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

