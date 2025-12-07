<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TagStoreRequest;
use App\Http\Requests\Api\TagUpdateRequest;
use App\Http\Resources\Api\TagCollection;
use App\Http\Resources\Api\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TagController extends Controller
{
    public function index(Request $request): TagCollection
    {
        $tags = Tag::all();

        return new TagCollection($tags);
    }

    public function store(TagStoreRequest $request): TagResource
    {
        $tag = Tag::create($request->validated());

        return new TagResource($tag);
    }

    public function show(Request $request, Tag $tag): TagResource
    {
        return new TagResource($tag);
    }

    public function update(TagUpdateRequest $request, Tag $tag): TagResource
    {
        $tag->update($request->validated());

        return new TagResource($tag);
    }

    public function destroy(Request $request, Tag $tag): Response
    {
        $tag->delete();

        return response()->noContent();
    }
}
