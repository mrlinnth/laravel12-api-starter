<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CommentStoreRequest;
use App\Http\Requests\Api\CommentUpdateRequest;
use App\Http\Resources\Api\CommentResource;
use App\Models\Comment;
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

    public function store(CommentStoreRequest $request): JsonResponse
    {
        $comment = Comment::create($request->validated());

        return $this->createdResponse(
            new CommentResource($comment),
            'Comment created successfully'
        );
    }

    public function update(CommentUpdateRequest $request, Comment $comment): JsonResponse
    {
        $comment->update($request->validated());

        return $this->successResponse(
            new CommentResource($comment),
            'Comment updated successfully'
        );
    }

    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        $comment->delete();

        return $this->deletedResponse('Comment deleted successfully');
    }
}
