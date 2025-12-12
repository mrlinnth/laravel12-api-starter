<?php

namespace App\Http\Controllers\Api;

use App\Data\CommentData;
use App\Http\Resources\Api\CommentResource;
use App\Models\Comment;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;

class CommentController extends BaseApiController
{
    protected function model(): string
    {
        return Comment::class;
    }

    protected function resource(): string
    {
        return CommentResource::class;
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::exact('post_id'),
            AllowedFilter::exact('user_id'),
            'content',
        ];
    }

    protected function allowedSorts(): array
    {
        return ['created_at', 'id'];
    }

    protected function allowedIncludes(): array
    {
        return ['post', 'user'];
    }

    /**
     * Display a paginated listing of comments.
     *
     * @response array{success: bool, data: CommentResource[], links: array{first: string|null, last: string|null, prev: string|null, next: string|null}, meta: array{current_page: int, from: int|null, last_page: int, path: string, per_page: int, to: int|null, total: int}}
     */
    #[QueryParameter('filter', description: 'Filter results by field values. Use filter[field]=value format. Available fields: post_id, user_id, content', type: 'object', required: false, example: ['post_id' => 1, 'user_id' => 1])]
    #[QueryParameter('sort', description: 'Sort results by field. Prefix with - for descending order. Available fields: created_at, id', type: 'string', required: false, example: '-created_at')]
    #[QueryParameter('include', description: 'Include related resources. Comma-separated list. Available relationships: post, user', type: 'string', required: false, example: 'user,post')]
    #[QueryParameter('per_page', description: 'Number of items per page for pagination.', type: 'integer', required: false, default: 15, example: 20)]
    #[QueryParameter('page', description: 'Page number for pagination.', type: 'integer', required: false, default: 1, example: 1)]
    public function index(Request $request): JsonResponse
    {
        return parent::index($request);
    }

    /**
     * Display the specified comment.
     *
     * @response array{success: bool, data: CommentResource}
     */
    #[PathParameter('id', description: 'The ID of the comment to retrieve.', type: 'integer', example: 1)]
    #[QueryParameter('include', description: 'Include related resources. Comma-separated list. Available relationships: post, user', type: 'string', required: false, example: 'user,post')]
    public function show(Request $request, $id): JsonResponse
    {
        return parent::show($request, $id);
    }

    /**
     * Store a newly created comment.
     *
     * @status 201
     *
     * @response array{success: bool, message: string, data: CommentResource}
     */
    #[BodyParameter('content', description: 'The content of the comment.', type: 'string', required: true, example: 'This is a great post!')]
    #[BodyParameter('post_id', description: 'The ID of the post this comment belongs to.', type: 'integer', required: true, example: 1)]
    #[BodyParameter('user_id', description: 'The ID of the user creating the comment.', type: 'integer', required: true, example: 1)]
    public function store(CommentData $data): JsonResponse
    {
        $comment = Comment::create($data->toArray());

        return $this->createdResponse(
            new CommentResource($comment),
            'Comment created successfully',
        );
    }

    /**
     * Update the specified comment.
     *
     * @response array{success: bool, message: string, data: CommentResource}
     */
    #[PathParameter('comment', description: 'The comment to update.', type: 'integer', example: 1)]
    #[BodyParameter('content', description: 'The content of the comment.', type: 'string', required: true, example: 'This is an updated comment!')]
    #[BodyParameter('post_id', description: 'The ID of the post this comment belongs to.', type: 'integer', required: true, example: 1)]
    #[BodyParameter('user_id', description: 'The ID of the user who owns the comment.', type: 'integer', required: true, example: 1)]
    public function update(CommentData $data, Comment $comment): JsonResponse
    {
        $comment->update($data->toArray());

        return $this->successResponse(
            new CommentResource($comment),
            'Comment updated successfully'
        );
    }

    /**
     * Remove the specified comment.
     *
     * @response array{success: bool, message: string}
     */
    #[PathParameter('comment', description: 'The comment to delete.', type: 'integer', example: 1)]
    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        return $this->deletedResponse('Comment deleted successfully');
    }
}
