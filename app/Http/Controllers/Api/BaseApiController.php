<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

abstract class BaseApiController extends Controller
{
    use ApiResponse;

    abstract protected function model(): string;

    abstract protected function resource(): string;

    /**
     * Fields that can be used in ?filter[field]=value
     */
    protected function allowedFilters(): array
    {
        return [];
    }

    /**
     * Fields that can be used in ?sort=field
     */
    protected function allowedSorts(): array
    {
        return ['created_at'];
    }

    /**
     * Relationships that can be included with ?include=relationship
     */
    protected function allowedIncludes(): array
    {
        return [];
    }

    /**
     * Relationships that are always loaded
     */
    protected function defaultIncludes(): array
    {
        return [];
    }

    /**
     * Fields that can be selected with ?fields[resource]=field1,field2
     */
    protected function allowedFields(): array
    {
        return [];
    }

    /**
     * Build the base query with Spatie Query Builder
     */
    protected function buildQuery(): QueryBuilder
    {
        $query = QueryBuilder::for($this->model())
            ->allowedFilters($this->allowedFilters())
            ->allowedSorts($this->allowedSorts())
            ->allowedIncludes($this->allowedIncludes());

        // Add default includes
        if (! empty($this->defaultIncludes())) {
            $query->with($this->defaultIncludes());
        }

        // Add field selection if configured
        if (! empty($this->allowedFields())) {
            $query->allowedFields($this->allowedFields());
        }

        return $query;
    }

    /**
     * Hook for custom query modifications
     */
    protected function modifyQuery(QueryBuilder $query, Request $request): QueryBuilder
    {
        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->buildQuery();
        $query = $this->modifyQuery($query, $request);

        $perPage = $request->get('per_page', 15);

        return $this->successResponse(
            $this->resource()::collection($query->paginate($perPage))
        );
    }

    public function show(Request $request, $id)
    {
        $query = $this->buildQuery();
        $query = $this->modifyQuery($query, $request);

        $model = $query->findOrFail($id);

        return $this->successResponse(
            new ($this->resource())($model)
        );
    }
}
