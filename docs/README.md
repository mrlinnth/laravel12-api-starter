# Laravel 12 REST API Starter

A production-ready Laravel 12 REST API starter with automatic OpenAPI documentation, advanced query capabilities, comprehensive testing, and modern development tools.

## Welcome to the Documentation

This documentation is bundled inside the project itself under `/docs` directory.

## Quick Start

```bash
# Clone the repository
git clone https://github.com/mrlinnth/laravel12-api-starter
cd laravel12-api-starter

# Run setup script
composer run setup

# Start development server
composer run dev
```
Access the application:
- **API**: http://localhost:8000/api
- **API Documentation**: http://localhost:8000/docs/api
- **Documentation**: http://localhost:8000/readme
- **Telescope**: http://localhost:8000/telescope

---

## Blueprint Code Generation

Blueprint is used to rapidly scaffold API resources with intelligent defaults.

### Quick Blueprint Start

Create a Blueprint YAML file and run:

```bash
php artisan blueprint:build your-draft.yaml
```

This will generate:
- Model with relationships and casts
- Migration with proper column types
- Factory with realistic fake data
- API Controller with query builder support
- Data DTO with type-safe properties
- API Resource for response transformation
- Feature test with assertions

### Example Blueprint File

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

## API Query Capabilities

All API endpoints support advanced querying:

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
### API Response Helpers

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