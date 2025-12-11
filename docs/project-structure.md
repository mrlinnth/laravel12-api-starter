# Project Structure

```
laravel12-api/
├── app/
│   ├── Blueprint/
│   │   └── Generators/  # Custom Blueprint generators (ApiControllerGenerator)
│   ├── Console/          # Artisan commands (auto-registered in Laravel 12)
│   ├── Data/            # Spatie Data DTOs for type-safe data handling
│   ├── Enums/           # PHP Enums with string backing (PostStatus, etc.)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── BaseApiController.php  # Base controller with query building
│   │   │       └── *Controller.php        # RESTful API controllers
│   │   ├── Requests/
│   │   │   └── Api/     # Form Request validation classes
│   │   └── Resources/
│   │       └── Api/     # API Resources & Collections
│   ├── Models/          # Eloquent models with relationships
│   ├── Providers/       # Service providers
│   └── Traits/
│       ├── ApiResponse.php  # Standardized JSON response methods
│       ├── HasUserId.php    # Auto-assign user_id trait
│       └── EnumArray.php    # Enum utilities
├── bootstrap/
│   ├── app.php          # Application configuration (Laravel 12 style)
│   └── providers.php    # Service provider registration
├── config/
│   ├── blueprint.php    # Blueprint configuration with custom generators
│   └── ...             # Other configuration files
├── database/
│   ├── factories/       # Model factories for testing
│   ├── migrations/      # Database migrations
│   └── seeders/        # Database seeders
├── public/             # Public assets and index.php
├── resources/
│   ├── views/          # Blade templates
│   ├── css/            # Frontend CSS
│   └── js/             # Frontend JavaScript
├── routes/
│   ├── api.php         # API routes (prefixed with /api)
│   ├── web.php         # Web routes
│   └── console.php     # Console routes
├── storage/            # Application storage (logs, cache, uploads)
├── stubs/
│   └── blueprint/      # Custom Blueprint stub files for code generation
│       ├── api-controller.class.stub
│       ├── api-controller.method.store.stub
│       ├── api-controller.method.update.stub
│       └── api-controller.method.destroy.stub
├── tests/
│   ├── Feature/        # Feature tests for controllers, APIs
│   ├── Unit/          # Unit tests for models, services
│   └── Browser/       # Browser tests (Pest v4)
└── vendor/            # Composer dependencies
```

## Key Architectural Patterns

- **Laravel 12 Structure**: No `app/Http/Middleware/` or `app/Console/Kernel.php`
- **BaseApiController Pattern**: All API controllers extend `BaseApiController` for consistent query building
- **ApiResponse Trait**: Standardized JSON responses across all API endpoints
- **Data Layer**: Spatie Data objects for type-safe DTOs with automatic validation from property types
- **API Resources**: Transform Eloquent models to consistent JSON responses
- **Data Object Validation**: Type-hinted properties automatically generate validation rules
- **Traits**: Shared behaviors (e.g., `HasUserId` auto-assigns authenticated user)

## BaseApiController & ApiResponse Trait

This project uses a custom **BaseApiController** that provides:

- **Automatic Query Building** with Spatie Query Builder integration
- **Consistent API Responses** via ApiResponse trait
- **DRY Principle** - Define model, resource, filters, sorts, and includes once

**BaseApiController Features:**

```php
abstract class BaseApiController extends Controller
{
    use ApiResponse;

    // Define these in your controller
    abstract protected function model(): string;
    abstract protected function resource(): string;

    protected function allowedFilters(): array { return []; }
    protected function allowedSorts(): array { return ['created_at']; }
    protected function allowedIncludes(): array { return []; }
    protected function defaultIncludes(): array { return []; }
    protected function allowedFields(): array { return []; }

    // Pre-built methods
    public function index(Request $request) { /* ... */ }
    public function show(Request $request, $id) { /* ... */ }

    // Hooks for customization
    protected function modifyQuery(QueryBuilder $query, Request $request) { /* ... */ }
}
```

**ApiResponse Trait Methods:**

- `successResponse($data, $message, $status = 200)` - Standard success response
- `createdResponse($data, $message)` - 201 Created response
- `deletedResponse($message)` - 200 OK for deletions
- `noContentResponse()` - 204 No Content
- `errorResponse($message, $status, $errors)` - Error response
- `validationErrorResponse($errors, $message)` - 422 Validation errors
- `notFoundResponse($message)` - 404 Not Found
- `unauthorizedResponse($message)` - 401 Unauthorized
- `forbiddenResponse($message)` - 403 Forbidden
- `badRequestResponse($message)` - 400 Bad Request
- `conflictResponse($message)` - 409 Conflict
- `serverErrorResponse($message)` - 500 Internal Server Error

**Example Controller:**

```php
class PostController extends BaseApiController
{
    protected function model(): string
    {
        return Post::class;
    }

    protected function resource(): string
    {
        return PostResource::class;
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::exact('status'),
            AllowedFilter::exact('user_id'),
            'title',
            'content',
        ];
    }

    protected function allowedSorts(): array
    {
        return ['created_at', 'published_at', 'title', 'id'];
    }

    protected function allowedIncludes(): array
    {
        return ['user', 'comments', 'tags'];
    }

    // Only need to implement store, update, destroy
    // index() and show() are provided by BaseApiController

    public function store(PostData $data): JsonResponse
    {
        $post = Post::create($data->toArray());

        return $this->createdResponse(
            new PostResource($post),
            'Post created successfully'
        );
    }
}
```

**Response Format:**

All API responses follow this consistent structure:

```json
{
  "success": true,
  "data": { /* resource data */ },
  "message": "Operation completed successfully"
}
```

For errors:
```json
{
  "success": false,
  "message": "Error message",
  "errors": { /* validation errors or details */ }
}
```
