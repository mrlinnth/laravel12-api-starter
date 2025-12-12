# Development

## Installed Packages

### Core Packages
- **[Laravel Framework 12](https://github.com/laravel/framework)** - Modern PHP framework _[website](https://laravel.com/)_
- **[Filament 4](https://github.com/filamentphp/filament)** - Admin panel and form builder _[website](https://filamentphp.com/)_
- **[Laravel Telescope 5](https://github.com/laravel/telescope)** - Debugging and monitoring tool _[website](https://laravel.com/docs/telescope)_

### API & Documentation
- **[Dedoc Scramble](https://github.com/dedoc/scramble)** - Automatic OpenAPI documentation generation _[website](https://scramble.dedoc.co/)_
- **[Spatie Laravel Data](https://github.com/spatie/laravel-data)** - Type-safe data transfer objects _[website](https://spatie.be/docs/laravel-data)_
- **[Spatie Laravel Query Builder](https://github.com/spatie/laravel-query-builder)** - Advanced API query capabilities _[website](https://spatie.be/docs/laravel-query-builder)_

### Authentication & Authorization
- **[Spatie Laravel Permission](https://github.com/spatie/laravel-permission)** - Role and permission management _[website](https://spatie.be/docs/laravel-permission)_

### Development Tools
- **[Laravel Pint](https://github.com/laravel/pint)** - Code style fixer (PSR-12) _[website](https://laravel.com/docs/pint)_
- **[Pest 4](https://github.com/pestphp/pest)** - Modern testing framework _[website](https://pestphp.com/)_
- **[Laravel IDE Helper](https://github.com/barryvdh/laravel-ide-helper)** - IDE autocompletion support
- **[Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar)** - Development debugging toolbar _[website](http://phpdebugbar.com/)_
- **[Laravel Sail](https://github.com/laravel/sail)** - Docker development environment _[website](https://laravel.com/docs/sail)_
- **[Laravel Pail](https://github.com/laravel/pail)** - Real-time log viewer _[website](https://laravel.com/docs/pail)_
- **[Blueprint](https://github.com/laravel-shift/blueprint)** - Model and migration generator _[website](https://blueprint.laravelshift.com/)_
- **[Nimbus](https://github.com/laracraft-tech/laravel-useful-additions)** - Additional development utilities

## Workflow to Add New Resource

This project follows a structured approach for creating API resources. You can choose between two workflows:

### Option A: Using Blueprint (Recommended)

**Laravel Blueprint** is integrated into this project with a **custom ApiControllerGenerator** that automatically creates controllers following the `BaseApiController` pattern.

**1. Create a draft.yaml file:**

```yaml
models:
  Product:
    id
    name: string:255
    description: longtext nullable
    price: decimal:8,2
    status: enum:draft,active,archived
    category_id: id foreign
    sku: string:100 unique
    published_at: nullable timestamp
    softDeletes
    timestamps
    relationships:
      belongsTo: Category
      hasMany: Review

controllers:
  Api\Product:
    resource: api
```

**2. Run Blueprint:**

```bash
php artisan blueprint:build draft.yaml
```

This single command generates:
- ✅ Model with proper relationships and casts
- ✅ Migration with all fields
- ✅ Factory with realistic fake data
- ✅ API Controller extending `BaseApiController` with:
  - Smart filters (exact for enums/foreign keys, partial for text)
  - Smart sorts (created_at, published_at, name, etc.)
  - Auto-configured relationship includes
  - Store, update, destroy methods using ApiResponse trait
- ✅ Form Requests (ProductStoreRequest, ProductUpdateRequest)
- ✅ API Resources (ProductResource)
- ✅ Feature tests
- ✅ Routes in routes/api.php

**Custom ApiControllerGenerator Features:**
- Automatically extends `BaseApiController` instead of standard Controller
- Smart filter detection based on column types (exact for FK/enums, partial for strings)
- Auto-generates allowedFilters(), allowedSorts(), allowedIncludes()
- Uses ApiResponse trait methods (createdResponse, successResponse, deletedResponse)
- Only generates store/update/destroy (index/show are in BaseApiController)
- Generates controllers using Spatie Data objects instead of Form Requests
- Proper imports for Data objects and Resources

**Generated Controller Example:**

```php
class ProductController extends BaseApiController
{
    protected function model(): string
    {
        return Product::class;
    }

    protected function resource(): string
    {
        return ProductResource::class;
    }

    protected function allowedFilters(): array
    {
        return [
            'name',
            AllowedFilter::exact('status'),
            AllowedFilter::exact('category_id'),
            'sku',
        ];
    }

    protected function allowedSorts(): array
    {
        return ['name', 'published_at', 'id'];
    }

    protected function allowedIncludes(): array
    {
        return ['category', 'review'];
    }

    public function store(ProductData $data): JsonResponse
    {
        $product = Product::create($data->toArray());
        return $this->createdResponse(
            new ProductResource($product),
            'Product created successfully'
        );
    }

    public function update(ProductData $data, Product $product): JsonResponse
    {
        $product->update($data->toArray());
        return $this->successResponse(
            new ProductResource($product),
            'Product updated successfully'
        );
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return $this->deletedResponse('Product deleted successfully');
    }
}
```

**3. Create Data Object:**

After Blueprint generates the controller, create a corresponding Data object:

```bash
php artisan make:class Data/ProductData
```

Define your Data object with typed properties:

```php
<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class ProductData extends Data
{
    public function __construct(
        public string $name,
        public ?string $description,
        public float $price,
        public int $stock,
        public int $category_id,
        public string $status,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?\DateTimeInterface $published_at,
    ) {}
}
```

**Key Features of Data Objects:**
- **Automatic Validation** - Validation rules are auto-generated from property types
- **Type Safety** - Strong typing throughout the request/response cycle
- **No Form Requests Needed** - Data objects handle validation automatically
- **Direct Controller Injection** - Type-hint Data objects in controller methods
- **Easy Conversion** - Use `$data->toArray()` for model creation/updates

**4. Customize if needed:**
- Edit generated files to add custom logic
- Add validation attributes to Data object properties
- Update Resource transformations

**5. Format and test:**

```bash
vendor/bin/pint --dirty
php artisan test
```

### Option B: Manual Generation

If you prefer manual control or need custom logic:

**1. Generate Model with migrations, factory, and seeder**

```bash
php artisan make:model Post --migration --factory --seed --no-interaction
```

**2. Update the migration file**

- Define table schema in `database/migrations/`
- Run migration: `php artisan migrate`

**3. Update Model**

- Define `$fillable` attributes
- Add relationships with return type hints (`HasMany`, `BelongsTo`, etc.)
- Define `casts()` method for type casting
- Add traits if needed (`HasFactory`, `HasUserId`)

**4. Create Data Object**

```bash
php artisan make:class Data/PostData --no-interaction
```
- Extend `Spatie\LaravelData\Data`
- Define typed properties with attributes
- Use `#[WithCast]` for custom casting (dates, enums, etc.)

Example:
```php
class PostData extends Data
{
    public function __construct(
        public string $title,
        public string $content,
        #[WithCast(EnumCast::class, type: PostStatus::class)]
        public PostStatus $status,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?CarbonImmutable $published_at,
    ) {}
}
```

**5. Generate API Controller**

```bash
php artisan make:controller Api/PostController --api --no-interaction
```
- Extend `BaseApiController`
- Implement CRUD methods (store, update, destroy)
- Use Data objects for validation (automatic from type hints)
- Return API Resources

Example:
```php
public function store(PostData $data): JsonResponse
{
    $post = Post::create($data->toArray());
    return $this->createdResponse(new PostResource($post), 'Post created successfully');
}
```

**6. Generate API Resources**

```bash
php artisan make:resource Api/PostResource --no-interaction
php artisan make:resource Api/PostCollection --no-interaction
```
- Define response structure in `toArray()` method
- Use `whenLoaded()` for relationships

**7. Register API routes**

Add resource route in `routes/api.php`:
```php
Route::apiResource('posts', App\Http\Controllers\Api\PostController::class);
```

**8. Update Factory**

Define realistic fake data in `database/factories/PostFactory.php`

**9. Create Tests**

```bash
php artisan make:test Feature/Http/Controllers/Api/PostControllerTest --no-interaction
```
- Test all CRUD operations
- Test validation (Data objects auto-validate)
- Use `RefreshDatabase` trait
- Use specific assertions (`assertCreated`, `assertOk`)

**10. Run tests and format code**

```bash
php artisan test --filter=PostControllerTest
vendor/bin/pint --dirty
```

**11. Verify API documentation**

Visit `/docs/api` to see auto-generated OpenAPI documentation

## Useful Commands

### Development

```bash
# Start all development services (server, queue, logs, vite)
composer run dev

# Start Laravel development server only
php artisan serve

# Watch and compile frontend assets
npm run dev

# Build frontend assets for production
npm run build
```

### Testing

```bash
# Run all tests
composer run test
# or
php artisan test

# Run specific test file
php artisan test tests/Feature/Http/Controllers/Api/PostControllerTest.php

# Filter by test name
php artisan test --filter=testName

# With coverage
php artisan test --coverage

# Parallel testing
php artisan test --parallel

# Stop on failure
php artisan test --stop-on-failure
```

### Database

```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh database with seeders
php artisan migrate:fresh --seed

# Seed database
php artisan db:seed

# Check database connection
php artisan db:show
```

### Code Quality

```bash
# Format code with Laravel Pint
vendor/bin/pint

# Format only changed files (recommended)
vendor/bin/pint --dirty

# Check code style without fixing
vendor/bin/pint --test
```

### IDE Support

```bash
# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models
php artisan ide-helper:meta
```

### Debugging

```bash
# View logs in real-time
php artisan pail

# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# List all routes
php artisan route:list

# Application information
php artisan about
```

### Queue Management

```bash
# Run queue worker
php artisan queue:work

# Listen to queue with auto-reload
php artisan queue:listen

# Restart queue workers
php artisan queue:restart

# Clear failed jobs
php artisan queue:flush
```

### Resource Management

```bash
# Delete all files related to an API resource
php artisan project:delete-resource Comment

# This command will:
# - Show a warning and require confirmation
# - Delete Data, Controller, Requests, Resources, Model, Factory, and Seeder files
# - Display list of deleted files
# - Show manual cleanup instructions for migrations, routes, and references
```

## Code Style

This project uses **Laravel Pint** for code formatting, enforcing PSR-12 coding standards with Laravel-specific conventions.

### Code Standards

- Always use curly braces for control structures, even single-line statements
- Use PHP 8 constructor property promotion
- Always use explicit return type declarations for methods
- Use appropriate PHP type hints for method parameters
- Array-based validation rules: `['required', 'string', 'max:400']`
- Prefer PHPDoc blocks over inline comments
- Enum keys should be TitleCase
- Use `casts()` method for model type casting (not `$casts` property)
- Never use `env()` directly in code - only in config files

### Formatting

Before committing code, always run:

```bash
vendor/bin/pint --dirty
```

This ensures your code follows the project's coding standards.

### Code Structure Conventions

- **Controllers**: Extend `BaseApiController`, define model/resource/filters/sorts/includes
- **Form Requests**: Array-based validation rules with explicit return types
- **Models**: Type-hinted relationships, use `casts()` method
- **API Resources**: Use `whenLoaded()` for optional relationships
- **Tests**: Use PHPUnit attributes (`#[Test]`), specific assertions (`assertOk()`, `assertCreated()`)

For comprehensive coding guidelines, see `.github/copilot-instructions.md` which contains Laravel Boost guidelines.
