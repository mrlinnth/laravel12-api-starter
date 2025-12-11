# BaseApiController Deep Dive

## Overview

All API controllers in this project extend `BaseApiController`, which provides:
- Automatic query building with Spatie Query Builder
- Built-in pagination support
- Standardized JSON responses via ApiResponse trait
- Consistent error handling
- DRY principle for common CRUD operations

## Configuration Methods

Define these abstract/protected methods in your controller:

```php
// Required abstract methods
abstract protected function model(): string;           // Return the model class
abstract protected function resource(): string;        // Return the API resource class

// Optional configuration methods
protected function allowedFilters(): array;            // Define filterable fields
protected function allowedSorts(): array;              // Define sortable fields
protected function allowedIncludes(): array;           // Define eager-loadable relationships
protected function defaultIncludes(): array;           // Relationships always loaded
protected function allowedFields(): array;             // Fields that can be selected
```

## Built-in Methods

**index(Request $request)**
- Automatically handles pagination (`?per_page=20`)
- Applies filters, sorts, includes based on query params
- Returns paginated resource collection
- Calls `modifyQuery()` hook for custom filtering

**show(Request $request, $id)**
- Finds model by ID with configured includes
- Applies allowed filters and relationships
- Returns single resource
- Throws 404 if not found

## Customization Hooks

**modifyQuery(QueryBuilder $query, Request $request): QueryBuilder**

Override this method to add custom query logic:

```php
protected function modifyQuery(QueryBuilder $query, Request $request): QueryBuilder
{
    // Add custom scopes
    if ($request->has('featured')) {
        $query->where('featured', true);
    }

    // Add user-specific filtering
    if (!$request->user()->isAdmin()) {
        $query->where('user_id', $request->user()->id);
    }

    return $query;
}
```

## ApiResponse Trait Reference

The `ApiResponse` trait provides standardized response methods:

**Success Responses:**
```php
// Generic success (200)
return $this->successResponse($data, 'Success message');

// Created (201)
return $this->createdResponse(
    new PostResource($post),
    'Post created successfully'
);

// Deleted (200)
return $this->deletedResponse('Post deleted successfully');

// No content (204)
return $this->noContentResponse();
```

**Error Responses:**
```php
// Bad request (400)
return $this->badRequestResponse('Invalid request');

// Unauthorized (401)
return $this->unauthorizedResponse('Authentication required');

// Forbidden (403)
return $this->forbiddenResponse('Access denied');

// Not found (404)
return $this->notFoundResponse('Post not found');

// Conflict (409)
return $this->conflictResponse('Duplicate entry');

// Validation error (422)
return $this->validationErrorResponse(
    $validator->errors(),
    'Validation failed'
);

// Server error (500)
return $this->serverErrorResponse('Something went wrong');

// Custom error
return $this->errorResponse('Custom error', 418, ['details' => 'Extra info']);
```

## Advanced Usage Examples

**Example 1: Custom Query Modification**

```php
class PostController extends BaseApiController
{
    protected function modifyQuery(QueryBuilder $query, Request $request): QueryBuilder
    {
        // Only show published posts to non-admin users
        if (!$request->user()?->hasRole('admin')) {
            $query->where('status', 'published');
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        return $query;
    }
}
```

**Example 2: Default Includes**

```php
protected function defaultIncludes(): array
{
    return ['user']; // Always load user relationship
}

protected function allowedIncludes(): array
{
    return ['user', 'comments', 'tags']; // Can optionally include these
}
```

**Example 3: Field Selection**

```php
protected function allowedFields(): array
{
    return ['id', 'title', 'status', 'created_at'];
}

// Request: GET /api/posts?fields=id,title
// Returns only id and title fields
```

**Example 4: Complex Filters**

```php
use Spatie\QueryBuilder\AllowedFilter;

protected function allowedFilters(): array
{
    return [
        AllowedFilter::exact('status'),           // Exact match
        AllowedFilter::exact('user_id'),          // Exact match for FK
        AllowedFilter::partial('title'),          // Partial match (LIKE)
        AllowedFilter::scope('published'),        // Model scope
        AllowedFilter::callback('min_price', function ($query, $value) {
            $query->where('price', '>=', $value);
        }),
    ];
}
```

## Response Structure

All responses follow this format:

**Success (200, 201):**
```json
{
  "success": true,
  "data": { /* resource or collection */ },
  "message": "Operation successful"
}
```

**Paginated Collection:**
```json
{
  "success": true,
  "data": [/* resources */],
  "links": {
    "first": "http://api.example.com/posts?page=1",
    "last": "http://api.example.com/posts?page=5",
    "prev": null,
    "next": "http://api.example.com/posts?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 15,
    "to": 15,
    "total": 73
  }
}
```

**Error (400-599):**
```json
{
  "success": false,
  "message": "Error description",
  "errors": { /* validation errors or additional details */ }
}
```
