<?php

namespace App\Http\Controllers\Api;

use App\Data\CommentData;
use App\Http\Resources\Api\CommentResource;
use App\Models\Comment;
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

    public function store(CommentData $data): JsonResponse
    {
        $comment = Comment::create($data->toArray());

        return $this->createdResponse(
            new CommentResource($comment),
            'Comment created successfully'
        );
    }

    public function update(CommentData $data, Comment $comment): JsonResponse
    {
        $comment->update($data->toArray());

        return $this->successResponse(
            new CommentResource($comment),
            'Comment updated successfully'
        );
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        return $this->deletedResponse('Comment deleted successfully');
    }
}
