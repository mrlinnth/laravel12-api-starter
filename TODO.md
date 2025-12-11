# TODO: API Controller Improvements

This document contains recommendations for improving the BaseApiController, ApiResponse trait, and related API controllers.

## High Priority

### 1. Remove or Prefix Unused `$request` Parameters
**Status:** Pending
**Files:** CommentController, PostController, TagController

The IDE warns about unused `$request` parameters in `store()`, `update()`, and `destroy()` methods.

**Options:**
- Remove if truly unused
- Keep for consistency with Laravel conventions and prefix with underscore: `$_request`

```php
// Option 1: Remove
public function destroy(Comment $comment): JsonResponse
{
    $comment->delete();
    return $this->deletedResponse('Comment deleted successfully');
}

// Option 2: Prefix with underscore
public function destroy(Request $_request, Comment $comment): JsonResponse
{
    $comment->delete();
    return $this->deletedResponse('Comment deleted successfully');
}
```

### 2. Add `per_page` Validation and Cap
**Status:** Pending
**File:** BaseApiController.php

Prevent abuse by validating and capping the `per_page` parameter.

```php
public function index(Request $request)
{
    $request->validate([
        'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
    ]);

    $query = $this->buildQuery();
    $query = $this->modifyQuery($query, $request);

    $perPage = min($request->get('per_page', 15), 100); // Cap at 100

    return $this->successResponse(
        $this->resource()::collection($query->paginate($perPage))
    );
}
```

### 3. Add Authorization Checks
**Status:** Pending
**Files:** All API Controllers

Add policy authorization to update/destroy methods.

```php
// In CommentController, PostController, TagController
public function update(CommentUpdateRequest $request, Comment $comment): JsonResponse
{
    $this->authorize('update', $comment);

    $comment->update($request->validated());

    return $this->successResponse(
        new CommentResource($comment),
        'Comment updated successfully'
    );
}

public function destroy(Request $request, Comment $comment): JsonResponse
{
    $this->authorize('delete', $comment);

    $comment->delete();

    return $this->deletedResponse('Comment deleted successfully');
}
```

### 4. Add Transaction Support for Complex Operations
**Status:** Pending
**Files:** Controllers with relationships (PostController)

Wrap complex operations in database transactions.

```php
use Illuminate\Support\Facades\DB;

public function store(PostStoreRequest $request): JsonResponse
{
    $post = DB::transaction(function () use ($request) {
        $post = Post::create($request->validated());

        // Handle tags if provided
        if ($request->has('tag_ids')) {
            $post->tags()->sync($request->input('tag_ids'));
        }

        return $post;
    });

    return $this->createdResponse(
        new PostResource($post->load(['user', 'tags'])),
        'Post created successfully'
    );
}
```

---

## Medium Priority

### 5. Add Exception Handling for Invalid Queries
**Status:** Pending
**File:** BaseApiController.php

Handle Spatie Query Builder exceptions gracefully.

```php
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;

public function index(Request $request)
{
    try {
        $query = $this->buildQuery();
        $query = $this->modifyQuery($query, $request);

        $perPage = $request->get('per_page', 15);

        return $this->successResponse(
            $this->resource()::collection($query->paginate($perPage))
        );
    } catch (InvalidFilterQuery $e) {
        return $this->badRequestResponse('Invalid filter: ' . $e->getMessage());
    }
}
```

### 6. Add Relationship Eager Loading in Store/Update
**Status:** Pending
**Files:** All API Controllers

Load relationships after creating/updating resources for consistent responses.

```php
public function store(CommentStoreRequest $request): JsonResponse
{
    $comment = Comment::create($request->validated());
    $comment->load(['post', 'user']); // Load relationships for response

    return $this->createdResponse(
        new CommentResource($comment),
        'Comment created successfully'
    );
}
```

### 7. Create Comprehensive Tests
**Status:** Pending
**Files:** tests/Feature/Http/Controllers/Api/*Test.php

Add tests for filtering, sorting, includes, and pagination.

```php
#[Test]
public function it_can_filter_posts_by_status(): void
{
    Post::factory()->create(['status' => PostStatus::Published]);
    Post::factory()->create(['status' => PostStatus::Draft]);

    $response = $this->getJson('/api/posts?filter[status]=published');

    $response->assertOk()
        ->assertJsonCount(1, 'data');
}

#[Test]
public function it_can_include_relationships(): void
{
    $post = Post::factory()->create();

    $response = $this->getJson('/api/posts/' . $post->id . '?include=user,comments');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'user',
                'comments',
            ],
        ]);
}

#[Test]
public function it_validates_per_page_parameter(): void
{
    $response = $this->getJson('/api/posts?per_page=500');

    $response->assertStatus(422);
}
```

### 8. Add Scopes Support
**Status:** Pending
**File:** BaseApiController.php

Add support for query scopes via Spatie Query Builder.

```php
// In BaseApiController
protected function allowedScopes(): array
{
    return [];
}

protected function buildQuery(): QueryBuilder
{
    $query = QueryBuilder::for($this->model())
        ->allowedFilters($this->allowedFilters())
        ->allowedSorts($this->allowedSorts())
        ->allowedIncludes($this->allowedIncludes())
        ->allowedScopes($this->allowedScopes()); // Add this

    // ... rest of method
}
```

Then in controllers:
```php
// In PostController
protected function allowedScopes(): array
{
    return ['published', 'draft'];
}
```

---

## Low Priority

### 9. Add Soft Delete Support
**Status:** Pending
**File:** BaseApiController.php

Support querying soft-deleted records.

```php
protected function withTrashed(): bool
{
    return false; // Override in child controllers
}

protected function buildQuery(): QueryBuilder
{
    $modelInstance = app($this->model());

    $query = QueryBuilder::for($this->model())
        ->allowedFilters($this->allowedFilters())
        ->allowedSorts($this->allowedSorts())
        ->allowedIncludes($this->allowedIncludes());

    if ($this->withTrashed() && method_exists($modelInstance, 'trashed')) {
        $query->withTrashed();
    }

    // ... rest of method
}
```

### 10. Add Bulk Operation Responses
**Status:** Pending
**File:** ApiResponse.php

Add helper for bulk operations.

```php
protected function bulkResponse(
    int $count,
    string $operation,
    int $status = 200
): JsonResponse {
    return response()->json([
        'success' => true,
        'message' => "{$count} items {$operation} successfully",
        'count' => $count,
    ], $status);
}
```

### 11. Add Additional Response Helpers
**Status:** Pending
**File:** ApiResponse.php

Add commonly needed response types.

```php
// Rate Limit Response (429)
protected function rateLimitResponse(
    ?string $message = 'Too many requests'
): JsonResponse {
    return $this->errorResponse($message, 429);
}

// Paginated Response Helper
protected function paginatedResponse(
    ResourceCollection $collection,
    ?string $message = null
): JsonResponse {
    return $collection
        ->additional([
            'success' => true,
            'message' => $message,
        ])
        ->response();
}
```

### 12. Enhance API Documentation
**Status:** Pending
**Files:** All API Controllers

Add PHPDoc blocks for better Scramble documentation generation.

```php
/**
 * List all comments with filtering, sorting, and pagination
 *
 * Query Parameters:
 * - filter[post_id]: Filter by post ID
 * - filter[user_id]: Filter by user ID
 * - filter[content]: Filter by content (partial match)
 * - sort: Sort by field (created_at, id)
 * - include: Include relationships (post, user)
 * - per_page: Items per page (default: 15, max: 100)
 *
 * @return JsonResponse
 */
public function index(Request $request)
{
    // ...
}
```

### 13. Add Authorization Hook in BaseApiController
**Status:** Pending
**File:** BaseApiController.php

Add a reusable authorization hook.

```php
protected function authorize(string $ability, mixed $model = null): void
{
    // Override in child controllers if needed
    // Can be used for custom authorization logic
}
```

### 14. Add Custom Filter Classes
**Status:** Pending
**Files:** API Controllers

Use custom filter classes for complex filtering scenarios.

```php
// In PostController
use App\Filters\FuzzyFilter;

protected function allowedFilters(): array
{
    return [
        AllowedFilter::exact('status'),
        AllowedFilter::exact('user_id'),
        AllowedFilter::scope('published_after'),
        AllowedFilter::custom('search', new FuzzyFilter('title', 'content')),
        'title',
        'content',
    ];
}
```

### 15. Add Rate Limiting Configuration
**Status:** Pending
**File:** bootstrap/app.php

Configure rate limiting for API routes.

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(append: [
        'throttle:api',
    ]);
})
```

---

## BaseApiController Enhancement Summary

### Proposed Additional Methods

```php
abstract class BaseApiController extends Controller
{
    use ApiResponse;

    abstract protected function model(): string;
    abstract protected function resource(): string;

    // Existing methods...
    protected function allowedFilters(): array { return []; }
    protected function allowedSorts(): array { return ['created_at']; }
    protected function allowedIncludes(): array { return []; }
    protected function defaultIncludes(): array { return []; }
    protected function allowedFields(): array { return []; }

    // NEW: Proposed additions
    protected function allowedScopes(): array { return []; }
    protected function withTrashed(): bool { return false; }
    protected function maxPerPage(): int { return 100; }
    protected function defaultPerPage(): int { return 15; }

    // NEW: Authorization hook
    protected function authorizeAction(string $ability, mixed $model = null): void
    {
        // Override in child controllers
    }

    // ENHANCED: buildQuery with scopes and soft deletes
    protected function buildQuery(): QueryBuilder
    {
        $query = QueryBuilder::for($this->model())
            ->allowedFilters($this->allowedFilters())
            ->allowedSorts($this->allowedSorts())
            ->allowedIncludes($this->allowedIncludes())
            ->allowedScopes($this->allowedScopes());

        if ($this->withTrashed()) {
            $query->withTrashed();
        }

        if (!empty($this->defaultIncludes())) {
            $query->with($this->defaultIncludes());
        }

        if (!empty($this->allowedFields())) {
            $query->allowedFields($this->allowedFields());
        }

        return $query;
    }

    // ENHANCED: index with validation and exception handling
    public function index(Request $request)
    {
        $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:' . $this->maxPerPage()],
        ]);

        try {
            $query = $this->buildQuery();
            $query = $this->modifyQuery($query, $request);

            $perPage = min(
                $request->get('per_page', $this->defaultPerPage()),
                $this->maxPerPage()
            );

            return $this->successResponse(
                $this->resource()::collection($query->paginate($perPage))
            );
        } catch (InvalidFilterQuery $e) {
            return $this->badRequestResponse('Invalid filter: ' . $e->getMessage());
        }
    }
}
```

---

## Notes

- Focus on High Priority items first
- Test each change thoroughly
- Update API documentation after implementing changes
- Consider backward compatibility when making changes
- Run `vendor/bin/pint --dirty` before committing
