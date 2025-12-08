# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 REST API application focused on providing well-structured API endpoints with comprehensive documentation. The application follows Laravel 12's streamlined architecture and uses modern PHP 8.4 features.

## Key Technologies

- **Laravel 12** with PHP 8.4
- **Filament v4** for admin panel
- **Spatie Laravel Data** for data transfer objects (DTOs)
- **Spatie Laravel Query Builder** for advanced API query capabilities (filtering, sorting, includes)
- **Spatie Laravel Permission** for role/permission management
- **Dedoc Scramble** for automatic OpenAPI documentation generation (accessible at `/docs/api`)
- **Laravel Telescope** for debugging and monitoring
- **Pest v4** for testing with PHPUnit attributes

## Architecture Patterns

### API Resource Structure

The application follows a consistent pattern for API resources:

1. **Models** (`app/Models/`) - Eloquent models with relationship methods and type-hinted return types
   - Use the `casts()` method for type casting (not `$casts` property)
   - Relationships always have explicit return types (`HasMany`, `BelongsTo`, etc.)

2. **Data Objects** (`app/Data/`) - Spatie Data DTOs for type-safe data handling
   - Used for structured data transfer with automatic validation
   - Include PHPDoc array shapes for complex arrays
   - Use `WithCast` attributes for custom casting (enums, dates, etc.)

3. **API Controllers** (`app/Http/Controllers/Api/`) - Simple, focused controllers
   - Follow RESTful conventions
   - Return API Resources or Collections
   - Delegate validation to Form Requests

4. **Form Requests** (`app/Http/Requests/Api/`) - Validation logic
   - Array-based validation rules (not string-based)
   - Separate requests for Store and Update operations

5. **API Resources** (`app/Http/Resources/Api/`) - Response transformation
   - Use `whenLoaded()` for optional relationships
   - Separate Resource and Collection classes

### Custom Traits

- **`HasUserId`** (`app/Traits/`) - Automatically sets `user_id` from authenticated user on model creation
- **`EnumArray`** (`app/Traits/`) - Provides array conversion utilities for enums

### Enums

- Enums use backed string values and TitleCase keys
- Include a `default()` method for default values
- Example: `PostStatus` enum with draft/published/archived states

## Common Development Commands

### Development Environment

```bash
# Start all development services (server, queue, logs, vite)
composer run dev

# Initial project setup
composer run setup

# Run tests
composer run test
php artisan test
php artisan test tests/Feature/ExampleTest.php
php artisan test --filter=testName
```

### Code Quality

```bash
# Format code (always run before finalizing changes)
vendor/bin/pint --dirty

# Generate IDE helper files (auto-runs on composer update)
php artisan ide-helper:generate
php artisan ide-helper:meta
```

### API Documentation

```bash
# View auto-generated API docs
# Navigate to /docs/api in browser
```

### Database

```bash
# Run migrations
php artisan migrate

# Fresh database with seeders
php artisan migrate:fresh --seed
```

### Creating New Resources

When creating a new API resource, follow this workflow:

```bash
# 1. Generate model with migration, factory, and seeder
php artisan make:model Post --migration --factory --seed

# 2. Generate API controller
php artisan make:controller Api/PostController --api

# 3. Generate Form Requests
php artisan make:request Api/PostStoreRequest
php artisan make:request Api/PostUpdateRequest

# 4. Generate API Resources
php artisan make:resource Api/PostResource
php artisan make:resource Api/PostCollection

# 5. Generate Data object
php artisan make:class Data/PostData

# 6. Generate test
php artisan make:test Feature/Http/Controllers/Api/PostControllerTest
```

## Testing Conventions

- Tests use **PHPUnit attributes** (`#[Test]`) not `test_` prefix or `/** @test */`
- Tests use **Pest v4** features but PHPUnit-style class structure
- Common traits: `RefreshDatabase`, `WithFaker`, `AdditionalAssertions` (from JMac)
- Use specific assertion methods: `assertOk()`, `assertCreated()`, `assertNoContent()` instead of `assertStatus()`
- Use `fake()` for generating test data (consistent with factories)
- Tests are in `tests/Feature/` and `tests/Unit/` directories

## API Query Capabilities

The application uses Spatie Laravel Query Builder for advanced querying:

```
# Include relationships
GET /api/posts?include=user,comments,tags

# Filter results
GET /api/posts?filter[status]=published

# Sort results
GET /api/posts?sort=-created_at

# Select specific fields
GET /api/posts?fields=id,title,status
```

## Configuration Notes

- **Laravel 12 structure**: No `app/Http/Middleware/` directory, middleware registered in `bootstrap/app.php`
- **No Console Kernel**: Commands auto-register from `app/Console/Commands/`
- **Routes**: API routes in `routes/api.php` with `/api` prefix, web routes in `routes/web.php`
- **Never use `env()` directly** in application code - only in config files, then use `config()`

## Validation Patterns

Follow existing validation conventions:
- Array-based rules: `['required', 'string', 'max:400']`
- Enum validation uses string values: `['required', 'in:draft,published,archived']`
- Foreign keys: `['required', 'integer', 'exists:users,id']`

## Important Files

- `bootstrap/app.php` - Application configuration, middleware, routing
- `config/scramble.php` - API documentation configuration
- `config/query-builder.php` - Query builder parameters and behavior
- `.github/copilot-instructions.md` - Contains Laravel Boost guidelines (comprehensive coding standards)
