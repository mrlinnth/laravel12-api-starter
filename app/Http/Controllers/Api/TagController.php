<?php

namespace App\Http\Controllers\Api;

use App\Data\TagData;
use App\Http\Resources\Api\TagResource;
use App\Models\Tag;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends BaseApiController
{
    protected function model(): string
    {
        return Tag::class;
    }

    protected function resource(): string
    {
        return TagResource::class;
    }

    protected function allowedFilters(): array
    {
        return ['title'];
    }

    protected function allowedSorts(): array
    {
        return ['created_at', 'title', 'id'];
    }

    protected function allowedIncludes(): array
    {
        return ['posts'];
    }

    /**
     * Display a paginated listing of tags.
     *
     * @response array{success: bool, data: TagResource[], links: array{first: string|null, last: string|null, prev: string|null, next: string|null}, meta: array{current_page: int, from: int|null, last_page: int, path: string, per_page: int, to: int|null, total: int}}
     */
    #[QueryParameter('filter', description: 'Filter results by field values. Use filter[field]=value format. Available fields: title', type: 'object', required: false, example: ['title' => 'Laravel'])]
    #[QueryParameter('sort', description: 'Sort results by field. Prefix with - for descending order. Available fields: created_at, title, id', type: 'string', required: false, example: '-created_at')]
    #[QueryParameter('include', description: 'Include related resources. Comma-separated list. Available relationships: posts', type: 'string', required: false, example: 'posts')]
    #[QueryParameter('per_page', description: 'Number of items per page for pagination.', type: 'integer', required: false, default: 15, example: 20)]
    #[QueryParameter('page', description: 'Page number for pagination.', type: 'integer', required: false, default: 1, example: 1)]
    public function index(Request $request): JsonResponse
    {
        return parent::index($request);
    }

    /**
     * Display the specified tag.
     *
     * @response array{success: bool, data: TagResource}
     */
    #[PathParameter('id', description: 'The ID of the tag to retrieve.', type: 'integer', example: 1)]
    #[QueryParameter('include', description: 'Include related resources. Comma-separated list. Available relationships: posts', type: 'string', required: false, example: 'posts')]
    public function show(Request $request, $id): JsonResponse
    {
        return parent::show($request, $id);
    }

    /**
     * Store a newly created tag.
     *
     * @status 201
     *
     * @response array{success: bool, message: string, data: TagResource}
     */
    #[BodyParameter('title', description: 'The title of the tag. Must be unique.', type: 'string', required: true, example: 'Laravel')]
    public function store(TagData $data): JsonResponse
    {
        $tag = Tag::create($data->toArray());

        return $this->createdResponse(
            new TagResource($tag),
            'Tag created successfully'
        );
    }

    /**
     * Update the specified tag.
     *
     * @response array{success: bool, message: string, data: TagResource}
     */
    #[PathParameter('tag', description: 'The tag to update.', type: 'integer', example: 1)]
    #[BodyParameter('title', description: 'The title of the tag. Must be unique.', type: 'string', required: true, example: 'PHP')]
    public function update(TagData $data, Tag $tag): JsonResponse
    {
        $tag->update($data->toArray());

        return $this->successResponse(
            new TagResource($tag),
            'Tag updated successfully'
        );
    }

    /**
     * Remove the specified tag.
     *
     * @response array{success: bool, message: string}
     */
    #[PathParameter('tag', description: 'The tag to delete.', type: 'integer', example: 1)]
    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();

        return $this->deletedResponse('Tag deleted successfully');
    }
}
