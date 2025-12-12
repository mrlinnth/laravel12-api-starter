<?php

namespace App\Http\Controllers\Api;

use App\Data\CommentData;
use App\Http\Controllers\Api\BaseApiController;
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
        ];
    }

    protected function allowedSorts(): array
    {
        return ['id'];
    }

    protected function allowedIncludes(): array
    {
        return [
            'post',
            'user',
        ];
    }

    /**
     * Display a paginated listing of comments.
     *
     * @response array{success: bool, data: CommentResource[], links: array{first: string|null, last: string|null, prev: string|null, next: string|null}, meta: array{current_page: int, from: int|null, last_page: int, path: string, per_page: int, to: int|null, total: int}}
     */
    #[QueryParameter('filter', description: 'Filter results by field values. Use filter[field]=value format. Available fields: post_id, user_id', type: 'object', required: false, example: ['post_id' => 1])]
    #[QueryParameter('sort', description: 'Sort results by field. Prefix with - for descending order. Available fields: id', type: 'string', required: false, example: '-created_at')]
    #[QueryParameter('include', description: 'Include related resources. Comma-separated list. Available relationships: post, user', type: 'string', required: false, example: 'post,user')]
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
    #[QueryParameter('include', description: 'Include related resources. Comma-separated list. Available relationships: post, user', type: 'string', required: false, example: 'post,user')]
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
    #[BodyParameter('post_id', description: 'The ID of the Post.', type: 'integer', required: true, example: 1)]
    #[BodyParameter('content', description: 'The Content.', type: 'string', required: true, example: 'This is example content for the article...')]
    #[BodyParameter('user_id', description: 'The ID of the User.', type: 'integer', required: true, example: 1)]

    public function store(CommentData $data): JsonResponse
    {
        $comment = Comment::create($data->toArray());

        return $this->createdResponse(
            new CommentResource($comment),
            'Comment created successfully'
        );
    }

    /**
     * Update the specified comment.
     *
     * @response array{success: bool, message: string, data: CommentResource}
     */
    #[PathParameter('comment', description: 'The comment to update.', type: 'integer', example: 1)]
    #[BodyParameter('post_id', description: 'The ID of the Post.', type: 'integer', required: true, example: 1)]
    #[BodyParameter('content', description: 'The Content.', type: 'string', required: true, example: 'This is example content for the article...')]
    #[BodyParameter('user_id', description: 'The ID of the User.', type: 'integer', required: true, example: 1)]

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

