<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CommentStoreRequest;
use App\Http\Requests\Api\CommentUpdateRequest;
use App\Http\Resources\Api\CommentCollection;
use App\Http\Resources\Api\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    public function index(Request $request): CommentCollection
    {
        $comments = Comment::all();

        return new CommentCollection($comments);
    }

    public function store(CommentStoreRequest $request): CommentResource
    {
        $comment = Comment::create($request->validated());

        return new CommentResource($comment);
    }

    public function show(Request $request, Comment $comment): CommentResource
    {
        return new CommentResource($comment);
    }

    public function update(CommentUpdateRequest $request, Comment $comment): CommentResource
    {
        $comment->update($request->validated());

        return new CommentResource($comment);
    }

    public function destroy(Request $request, Comment $comment): Response
    {
        $comment->delete();

        return response()->noContent();
    }
}
