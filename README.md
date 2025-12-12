# Laravel 12 REST API Starter

A production-ready Laravel 12 REST API starter with automatic OpenAPI documentation, advanced query capabilities, comprehensive testing, and modern development tools.

**Demo** : https://api-starter.hiyan.xyz/docs/api

**Documentation** : https://api-starter.hiyan.xyz/readme

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
- **Telescope**: http://localhost:8000/telescoper

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
  Category:
    title: string:400 unique

  Product:
    category_id: id foreign
    name: string:200
    description: text nullable
    price: decimal:10,2
    stock: integer
    status: enum:draft,active,archived
    published_at: timestamp nullable
    softDeletes: true
    relationships:
      belongsTo: Category
      hasMany: Review

  Review:
    product_id: id foreign
    content: longtext
    user_id: id foreign
    relationships:
      belongsTo: Product, User

controllers:
  Api/Category:
    resource: api

  Api/Product:
    resource: api

  Api/Review:
    resource: api
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
GET /api/products?include=category,reviews.user
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

## Documentation

For detailed documentation, please refer to the following guides:

- **[Features](docs/features.md)** - Core API features, developer experience, and query capabilities
- **[Requirements](docs/requirements.md)** - System requirements and dependencies
- **[Project Structure](docs/project-structure.md)** - Codebase architecture and patterns
- **[Getting Started](docs/getting-started.md)** - Installation and environment setup
- **[Development](docs/development.md)** - Development workflow, commands, and code style
- **[BaseApiController](docs/base-api-controller.md)** - Deep dive into the base controller pattern
- **[API Usage](docs/api-usage.md)** - API endpoints and query examples
- **[Authentication](docs/authentication.md)** - Authentication and authorization setup
- **[Testing](docs/testing.md)** - Testing strategy and best practices
- **[Deployment](docs/deployment.md)** - Production deployment guide
- **[Troubleshooting](docs/troubleshooting.md)** - Common issues and solutions
- **[Contributing](docs/contributing.md)** - Contribution guidelines

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

### MIT License

Copyright (c) 2025 Laravel 12 REST API Starter

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
