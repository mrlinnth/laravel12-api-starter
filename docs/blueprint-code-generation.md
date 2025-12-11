# Blueprint Code Generation

This project includes custom Blueprint generators that automatically create advanced API resources with Spatie Query Builder integration, Data DTOs, and BaseApiController inheritance.

## Table of Contents

- [Overview](#overview)
- [Quick Start](#quick-start)
- [What Gets Generated](#what-gets-generated)
- [API Controller Generation](#api-controller-generation)
  - [BaseApiController Architecture](#baseapicontroller-architecture)
  - [Intelligent Filter Generation](#intelligent-filter-generation)
  - [Intelligent Sort Generation](#intelligent-sort-generation)
  - [Relationship Includes](#relationship-includes)
  - [Generated CRUD Methods](#generated-crud-methods)
- [Data DTO Generation](#data-dto-generation)
  - [Automatic Type Mapping](#automatic-type-mapping)
  - [Nullable Properties](#nullable-properties)
  - [Enum Support](#enum-support)
  - [DateTime Support](#datetime-support)
  - [Relationship Properties](#relationship-properties)
- [API Query Examples](#api-query-examples)
- [Customizing Generated Code](#customizing-generated-code)
- [Blueprint YAML Conventions](#blueprint-yaml-conventions)
- [Files and Configuration](#files-and-configuration)

## Overview

The custom Blueprint generators automatically create:

1. **API Controllers** - RESTful controllers extending BaseApiController with:
   - Spatie Query Builder for filtering, sorting, and includes
   - Intelligent defaults based on model structure
   - Data DTO integration for type-safe requests
   - API Resource responses

2. **Data DTOs** - Spatie Laravel Data classes with:
   - Automatic type hints from model columns
   - Enum and date casting attributes
   - Relationship properties
   - Nullable detection

## Quick Start

Create a Blueprint YAML file:

```yaml
models:
  Product:
    name: string:200
    description: text nullable
    price: decimal:10,2
    stock: integer
    status: enum:draft,active,archived
    published_at: timestamp nullable
    category_id: id foreign:categories
    relationships:
      belongsTo: Category
      hasMany: Review

controllers:
  Api/Product:
    index:
      query: all
    show:
      find: id
    store:
      validate: name, price, stock, status
      save: product
    update:
      find: id
      validate: name, price, stock, status
      save: product
    destroy:
      delete: id
```

Run the generator:

```bash
php artisan blueprint:build your-draft.yaml
```

## What Gets Generated

Running Blueprint generates a complete API resource:

- ✅ **Model** (`app/Models/Product.php`) - With relationships and casts
- ✅ **Migration** (`database/migrations/*_create_products_table.php`) - Proper column types
- ✅ **Factory** (`database/factories/ProductFactory.php`) - Realistic fake data
- ✅ **Seeder** (`database/seeders/ProductSeeder.php`) - Database seeding
- ✅ **API Controller** (`app/Http/Controllers/Api/ProductController.php`) - With query builder
- ✅ **Data DTO** (`app/Data/ProductData.php`) - Type-safe validation
- ✅ **API Resource** (`app/Http/Resources/Api/ProductResource.php`) - Response transformation
- ✅ **Feature Test** (`tests/Feature/Http/Controllers/Api/ProductControllerTest.php`) - Test coverage

---

## API Controller Generation

### Example Generated Controller

```php
<?php

namespace App\Http\Controllers\Api;

use App\Data\ProductData;
use App\Http\Resources\Api\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;

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
            'description',
            AllowedFilter::exact('status'),
            AllowedFilter::exact('category_id'),
        ];
    }

    protected function allowedSorts(): array
    {
        return ['name', 'published_at', 'id'];
    }

    protected function allowedIncludes(): array
    {
        return ['category', 'reviews'];
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

### BaseApiController Architecture

All generated API controllers extend `BaseApiController`, which provides:

#### Core Methods

- **`index()`** - Paginated list of resources with query builder support
- **`show($id)`** - Single resource retrieval

#### Abstract Methods (Must Implement)

```php
abstract protected function model(): string;
abstract protected function resource(): string;
```

#### Configurable Methods (Optional Overrides)

```php
protected function allowedFilters(): array;      // Fields for ?filter[field]=value
protected function allowedSorts(): array;        // Fields for ?sort=field,-field
protected function allowedIncludes(): array;     // Relationships for ?include=relation
protected function defaultIncludes(): array;     // Always-loaded relationships
protected function allowedFields(): array;       // Fields for ?fields[resource]=field1,field2
```

#### Query Customization Hook

```php
protected function modifyQuery(QueryBuilder $query, Request $request): QueryBuilder
{
    // Add custom query logic here
    return $query;
}
```

### Intelligent Filter Generation

The generator intelligently configures filters based on column types:

#### Exact Filters

Used for enums and foreign keys (exact match):

```php
AllowedFilter::exact('status')      // enum columns
AllowedFilter::exact('category_id') // foreign key columns
```

Query: `?filter[status]=active&filter[category_id]=5`

#### Partial Filters

Used for text fields (partial/fuzzy match):

```php
'name',         // string columns
'description',  // text columns
```

Query: `?filter[name]=laptop`

#### Excluded Columns

Automatically skipped:
- `id` (primary key)
- `created_at`, `updated_at`, `deleted_at` (timestamps)

### Intelligent Sort Generation

The generator automatically includes common sortable fields:

```php
protected function allowedSorts(): array
{
    return ['created_at', 'published_at', 'title', 'name', 'id'];
}
```

Only fields that exist on the model are included. Always includes `id` by default.

### Relationship Includes

The generator configures allowed includes from model relationships:

```php
protected function allowedIncludes(): array
{
    return ['category', 'reviews'];
}
```

This enables eager loading to prevent N+1 queries.

### Generated CRUD Methods

#### Store Method

```php
public function store(ProductData $data): JsonResponse
{
    $product = Product::create($data->toArray());

    return $this->createdResponse(
        new ProductResource($product),
        'Product created successfully'
    );
}
```

- Accepts Data DTO for automatic validation
- Creates the model
- Returns 201 Created response
- Wraps response in API Resource

#### Update Method

```php
public function update(ProductData $data, Product $product): JsonResponse
{
    $product->update($data->toArray());

    return $this->successResponse(
        new ProductResource($product),
        'Product updated successfully'
    );
}
```

- Route model binding for the model
- Accepts Data DTO for validation
- Updates the model
- Returns 200 OK response

#### Destroy Method

```php
public function destroy(Product $product): JsonResponse
{
    $product->delete();

    return $this->deletedResponse('Product deleted successfully');
}
```

- Route model binding
- Soft deletes if configured on model
- Returns 200 OK response

---

## Data DTO Generation

### Example Generated Data Class

```php
<?php

namespace App\Data;

use App\Data\CategoryData;
use App\Data\ReviewData;
use App\Enums\Status;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class ProductData extends Data
{
    public function __construct(
        public string $name,
        public ?string $description,
        public float $price,
        public int $stock,
        #[WithCast(EnumCast::class, type: Status::class)]
        public Status $status,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?CarbonImmutable $published_at,
        public CategoryData $category,
        /** @var array<ReviewData> */
        public array $reviews,
    ) {}
}
```

### Automatic Type Mapping

The generator intelligently maps Blueprint column types to PHP types:

| Blueprint Type | PHP Type |
|---|---|
| `string`, `text`, `longtext` | `string` |
| `integer`, `bigInteger`, etc. | `int` |
| `decimal`, `float`, `double` | `float` |
| `boolean` | `bool` |
| `json`, `jsonb` | `array` |
| `enum` | Custom enum class with `WithCast` |
| `date`, `datetime`, `timestamp` | `CarbonImmutable` with `WithCast` |

### Nullable Properties

Properties are automatically marked as nullable based on the Blueprint column definition:

```yaml
description: text nullable
```

Generates:

```php
public ?string $description
```

### Enum Support

Enum columns automatically get the proper casting:

```yaml
status: enum:draft,active,archived
```

Generates:

```php
#[WithCast(EnumCast::class, type: Status::class)]
public Status $status
```

The enum class name is inferred from the column name (e.g., `status` → `Status`).

### DateTime Support

Date columns automatically use `CarbonImmutable`:

```yaml
published_at: timestamp nullable
```

Generates:

```php
#[WithCast(DateTimeInterfaceCast::class)]
public ?CarbonImmutable $published_at
```

### Relationship Properties

Relationships are automatically included as properties:

**Single Relationships** (`belongsTo`, `hasOne`, `morphTo`, `morphOne`):
```php
public CategoryData $category
```

**Collection Relationships** (`hasMany`, `belongsToMany`, `morphMany`, `morphToMany`):
```php
/** @var array<ReviewData> */
public array $reviews
```

**Important Notes:**
- The generator skips `id`, `created_at`, `updated_at`, and `deleted_at` columns
- Foreign key columns (ending with `_id`) are skipped in favor of relationship properties
- Related Data classes are automatically imported

---

## API Query Examples

### Filtering

```bash
# Exact match on enum
GET /api/products?filter[status]=active

# Exact match on foreign key
GET /api/products?filter[category_id]=5

# Partial match on text fields
GET /api/products?filter[name]=laptop

# Combine multiple filters
GET /api/products?filter[status]=active&filter[category_id]=5&filter[name]=gaming
```

### Sorting

```bash
# Sort ascending
GET /api/products?sort=name

# Sort descending
GET /api/products?sort=-published_at

# Multiple sorts
GET /api/products?sort=status,-created_at
```

### Including Relationships

```bash
# Single relationship
GET /api/products?include=category

# Multiple relationships
GET /api/products?include=category,reviews

# Nested relationships
GET /api/products?include=category,reviews.author
```

### Pagination

```bash
# Default pagination (15 per page)
GET /api/products

# Custom per page
GET /api/products?per_page=25

# Specific page
GET /api/products?page=2&per_page=25
```

### Combining Query Parameters

```bash
GET /api/products?filter[status]=active&include=category,reviews&sort=-published_at&per_page=20
```

---

## Customizing Generated Code

### Override Query Logic

Add custom query modifications:

```php
protected function modifyQuery(QueryBuilder $query, Request $request): QueryBuilder
{
    // Only show published products for non-admin users
    if (!$request->user()?->isAdmin()) {
        $query->where('status', 'active');
    }

    return $query;
}
```

### Add Default Includes

Always load certain relationships:

```php
protected function defaultIncludes(): array
{
    return ['category'];  // Always load the category relationship
}
```

### Configure Field Selection

Enable sparse fieldsets:

```php
protected function allowedFields(): array
{
    return ['products' => ['id', 'name', 'price', 'status']];
}
```

Query: `?fields[products]=id,name,price`

### Add Custom Methods

```php
public function publish(Product $product): JsonResponse
{
    $product->update(['status' => ProductStatus::Active]);

    return $this->successResponse(
        new ProductResource($product),
        'Product published successfully'
    );
}
```

### Customize Stub Templates

You can customize the generation by editing stub files:

- `stubs/blueprint/api-controller.class.stub`
- `stubs/blueprint/api-controller.method.store.stub`
- `stubs/blueprint/api-controller.method.update.stub`
- `stubs/blueprint/api-controller.method.destroy.stub`
- `stubs/blueprint/data.class.stub`

---

## Blueprint YAML Conventions

### Controller Definition

Controllers in the `Api/` namespace are automatically processed:

```yaml
controllers:
  Api/Product:        # Creates ProductController in Api namespace
    index:
      query: all
    show:
      find: id
    store:
      validate: name, price
      save: product
    update:
      find: id
      validate: name, price
      save: product
    destroy:
      delete: id
```

### Required Controller Methods

For the generator to create proper CRUD methods:

- `index` - Required for index method (handled by BaseApiController)
- `show` - Required for show method (handled by BaseApiController)
- `store` - Required to generate store method
- `update` - Required to generate update method
- `destroy` - Required to generate destroy method

### Model Relationships

Define relationships in your model:

```yaml
models:
  Product:
    # ... columns ...
    relationships:
      belongsTo: Category, User
      hasMany: Review
      belongsToMany: Tag
```

This affects:
- **Data DTOs** - Adds relationship properties
- **Controllers** - Configures allowed includes

---

## Files and Configuration

### Generator Files

- **API Controller Generator**: [app/Blueprint/Generators/ApiControllerGenerator.php](../app/Blueprint/Generators/ApiControllerGenerator.php)
- **Data Generator**: [app/Blueprint/Generators/ApiDataGenerator.php](../app/Blueprint/Generators/ApiDataGenerator.php)
- **Base Controller**: [app/Http/Controllers/Api/BaseApiController.php](../app/Http/Controllers/Api/BaseApiController.php)

### Stub Files

- [stubs/blueprint/api-controller.class.stub](../stubs/blueprint/api-controller.class.stub)
- [stubs/blueprint/api-controller.method.store.stub](../stubs/blueprint/api-controller.method.store.stub)
- [stubs/blueprint/api-controller.method.update.stub](../stubs/blueprint/api-controller.method.update.stub)
- [stubs/blueprint/api-controller.method.destroy.stub](../stubs/blueprint/api-controller.method.destroy.stub)
- [stubs/blueprint/data.class.stub](../stubs/blueprint/data.class.stub)

### Configuration

[config/blueprint.php](../config/blueprint.php):

```php
'generators' => [
    // ... other generators
    'api_controller' => \App\Blueprint\Generators\ApiControllerGenerator::class,
    'api_data' => \App\Blueprint\Generators\ApiDataGenerator::class,
],
```

### Response Helpers

Generated controllers use these ApiResponse trait methods:

```php
$this->successResponse($data, $message = '');           // 200 OK
$this->createdResponse($data, $message = '');           // 201 Created
$this->deletedResponse($message = '');                  // 200 OK
$this->errorResponse($message, $code = 400, $errors);   // 4xx/5xx Error
$this->validationErrorResponse($errors);                // 422 Unprocessable Entity
```

All responses follow a consistent JSON structure:

```json
{
  "success": true,
  "message": "Resource retrieved successfully",
  "data": { ... }
}
```

---

## Benefits

1. **Consistent API Structure** - All controllers follow the same pattern
2. **Advanced Query Capabilities** - Filtering, sorting, includes out of the box
3. **Type Safety** - Data DTOs provide automatic validation
4. **DRY Principle** - BaseApiController eliminates code duplication
5. **Intelligent Defaults** - Smart configuration based on model schema
6. **Extensible** - Easy to customize per controller
7. **Performance** - Query Builder prevents N+1 queries
8. **Documentation Ready** - Works seamlessly with Scramble API docs
9. **Rapid Development** - Complete API resource in one command

## Related Documentation

- [Spatie Query Builder](https://spatie.be/docs/laravel-query-builder/) - Query capabilities
- [Spatie Laravel Data](https://spatie.be/docs/laravel-data/) - Data DTOs and validation
- [Blueprint](https://github.com/laravel-shift/blueprint) - Code generation tool
