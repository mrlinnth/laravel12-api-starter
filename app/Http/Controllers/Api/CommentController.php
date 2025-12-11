<?php

namespace App\Http\Controllers\Api;

use App\Data\CommentData;
use App\Http\Resources\Api\CommentResource;
use App\Models\Comment;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\PathParameter;
use Illuminate\Http\JsonResponse;
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

    #[BodyParameter('content', description: 'The content of the comment.', type: 'string', required: true, example: 'This is a great post!')]
    #[BodyParameter('post_id', description: 'The ID of the post this comment belongs to.', type: 'integer', required: true, example: 1)]
    #[BodyParameter('user_id', description: 'The ID of the user creating the comment.', type: 'integer', required: true, example: 1)]
    public function store(CommentData $data): JsonResponse
    {
        $comment = Comment::create($data->toArray());

        return $this->createdResponse(
            new CommentResource($comment),
            'Comment created successfully'
        );
    }

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

    #[PathParameter('comment', description: 'The comment to delete.', type: 'integer', example: 1)]
    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        return $this->deletedResponse('Comment deleted successfully');
    }
}
