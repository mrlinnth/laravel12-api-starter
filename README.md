# Laravel 12 REST API Starter

A starter repo with default configuration to quickly start a project to develop REST API endpoints.

## Requirements
- Laravel 12
- PHP 8.4
- Composer 2.9
- MySQL/Mariadb
- Apache/Nginx

## Installed Packages

### Core Packages
- **Laravel Framework 12** - Modern PHP framework
- **Filament 4** - Admin panel and form builder
- **Laravel Telescope 5** - Debugging and monitoring tool

### API & Documentation
- **Dedoc Scramble** - Automatic OpenAPI documentation generation
- **Spatie Laravel Data** - Type-safe data transfer objects
- **Spatie Laravel Query Builder** - Advanced API query capabilities (filtering, sorting, includes)

### Authentication & Authorization
- **Spatie Laravel Permission** - Role and permission management

### Development Tools
- **Laravel Pint** - Code style fixer
- **Pest 4** - Testing framework
- **Laravel IDE Helper** - IDE autocompletion support
- **Laravel Debugbar** - Development debugging toolbar
- **Laravel Sail** - Docker development environment
- **Laravel Pail** - Log viewer
- **Blueprint** - Model and migration generator
- **Nimbus** - Additional development utilities

## Local Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd laravel12-api
   ```

2. **Run setup script**
   ```bash
   composer run setup
   ```
   This will:
   - Install Composer dependencies
   - Copy `.env.example` to `.env`
   - Generate application key
   - Run migrations
   - Install and build frontend assets

3. **Configure environment**
   - Update `.env` file with your database credentials
   - Set `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

4. **Run migrations (if not already run)**
   ```bash
   php artisan migrate
   ```

5. **Seed database (optional)**
   ```bash
   php artisan db:seed
   ```

6. **Start development server**
   ```bash
   composer run dev
   ```
   This starts the Laravel server, queue worker, log viewer, and Vite dev server concurrently.

7. **Access the application**
   - API: `http://localhost:8000/api`
   - API Documentation: `http://localhost:8000/docs/api`
   - Admin Panel: `http://localhost:8000/admin`
   - Telescope: `http://localhost:8000/telescope`

## Workflow to Add New Resource

This project follows a structured approach to creating API resources. Here's the recommended workflow:

1. **Generate Model with migrations, factory, and seeder**
   ```bash
   php artisan make:model Post --migration --factory --seed --no-interaction
   ```

2. **Update the migration file**
   - Define table schema in `database/migrations/`
   - Run migration: `php artisan migrate`

3. **Update Model**
   - Define `$fillable` attributes
   - Add relationships with return type hints (`HasMany`, `BelongsTo`, etc.)
   - Define `casts()` method for type casting
   - Add traits if needed (`HasFactory`, `HasUserId`)

4. **Generate API Controller**
   ```bash
   php artisan make:controller Api/PostController --api --no-interaction
   ```
   - Implement CRUD methods (index, store, show, update, destroy)
   - Use Form Requests for validation
   - Return API Resources

5. **Generate Form Requests**
   ```bash
   php artisan make:request Api/PostStoreRequest --no-interaction
   php artisan make:request Api/PostUpdateRequest --no-interaction
   ```
   - Define validation rules (use array format)
   - Set `authorize()` method appropriately

6. **Generate API Resources**
   ```bash
   php artisan make:resource Api/PostResource --no-interaction
   php artisan make:resource Api/PostCollection --no-interaction
   ```
   - Define response structure in `toArray()` method
   - Use `whenLoaded()` for relationships

7. **Create Data Object (optional but recommended)**
   ```bash
   php artisan make:class Data/PostData --no-interaction
   ```
   - Extend `Spatie\LaravelData\Data`
   - Define typed properties with attributes

8. **Register API routes**
   - Add resource route in `routes/api.php`:
     ```php
     Route::apiResource('posts', App\Http\Controllers\Api\PostController::class);
     ```

9. **Update Factory**
   - Define realistic fake data in `database/factories/PostFactory.php`

10. **Create Tests**
    ```bash
    php artisan make:test Feature/Http/Controllers/Api/PostControllerTest --no-interaction
    ```
    - Test all CRUD operations
    - Test validation rules
    - Use `RefreshDatabase` trait

11. **Run tests and format code**
    ```bash
    php artisan test --filter=PostControllerTest
    vendor/bin/pint --dirty
    ```

12. **Verify API documentation**
    - Visit `/docs/api` to see auto-generated OpenAPI documentation

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
php artisan test tests/Feature/ExampleTest.php

# Run tests matching a filter
php artisan test --filter=testName

# Run tests with coverage
php artisan test --coverage
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
```

### Code Quality

```bash
# Format code with Laravel Pint
vendor/bin/pint

# Format only changed files
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
```

### Queue Management

```bash
# Run queue worker
php artisan queue:work

# Listen to queue with auto-reload
php artisan queue:listen
```

## Code Style

This project uses **Laravel Pint** for code formatting, which enforces PSR-12 coding standards with Laravel-specific conventions.

### Rules

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

- **Controllers**: Simple and focused, delegate to Form Requests for validation
- **Form Requests**: Array-based validation rules with explicit return types
- **Models**: Type-hinted relationships, use `casts()` method
- **API Resources**: Use `whenLoaded()` for optional relationships
- **Tests**: Use PHPUnit attributes (`#[Test]`), specific assertions (`assertOk()`, `assertCreated()`)

For comprehensive coding guidelines, see `.github/copilot-instructions.md` which contains Laravel Boost guidelines.

## Git Commit and PR Templates

This project does not currently have custom commit or PR templates, but follows these conventions:

### Commit Message Guidelines

Write clear, concise commit messages that follow this format:

```
type: brief description

Longer explanation if needed (optional)
```

**Types:**
- `feat:` New feature
- `fix:` Bug fix
- `refactor:` Code refactoring
- `test:` Adding or updating tests
- `docs:` Documentation changes
- `style:` Code style/formatting changes
- `chore:` Maintenance tasks

**Examples:**
```
feat: add post filtering by status

fix: resolve N+1 query in post index endpoint

test: add validation tests for PostStoreRequest

refactor: extract user assignment to HasUserId trait
```

### Pull Request Guidelines

When creating a pull request:

1. Ensure all tests pass (`php artisan test`)
2. Run code formatter (`vendor/bin/pint --dirty`)
3. Provide a clear title and description
4. List key changes and their purpose
5. Mention any breaking changes
6. Reference related issues if applicable

### Branch Naming

- `feature/` - New features (e.g., `feature/add-post-filtering`)
- `fix/` - Bug fixes (e.g., `fix/n-plus-one-posts`)
- `refactor/` - Code improvements (e.g., `refactor/optimize-queries`)
- `test/` - Test additions (e.g., `test/post-controller`)

### Current Branch Structure

- **main** - Production-ready code
- **develop** - Development branch (current working branch)

## API Usage & Examples

### Available Endpoints

This API provides RESTful endpoints for the following resources:

- **Posts** - `/api/posts`
- **Comments** - `/api/comments`
- **Tags** - `/api/tags`

Each resource supports standard CRUD operations:
- `GET /api/posts` - List all posts
- `GET /api/posts/{id}` - Get a single post
- `POST /api/posts` - Create a new post
- `PUT/PATCH /api/posts/{id}` - Update a post
- `DELETE /api/posts/{id}` - Delete a post

### Example Requests

#### Get All Posts

```bash
GET /api/posts
```

Response:
```json
{
  "data": [
    {
      "id": 1,
      "title": "Example Post",
      "content": "This is the post content...",
      "status": "published",
      "user_id": 1,
      "published_at": "2025-12-08T10:00:00.000000Z",
      "deleted_at": null
    }
  ]
}
```

#### Create a New Post

```bash
POST /api/posts
Content-Type: application/json

{
  "title": "New Post Title",
  "content": "Post content here...",
  "status": "draft",
  "user_id": 1,
  "published_at": "2025-12-08T10:00:00.000000Z"
}
```

#### Advanced Query Parameters

Using Spatie Laravel Query Builder, you can:

**Include relationships:**
```bash
GET /api/posts?include=user,comments,tags
```

**Filter results:**
```bash
GET /api/posts?filter[status]=published
GET /api/posts?filter[title]=example
```

**Sort results:**
```bash
GET /api/posts?sort=-created_at           # Descending
GET /api/posts?sort=title                  # Ascending
```

**Select specific fields:**
```bash
GET /api/posts?fields=id,title,status
```

**Combine multiple parameters:**
```bash
GET /api/posts?include=user&filter[status]=published&sort=-created_at
```

### API Documentation

Interactive API documentation with "Try It" feature is available at:
```
http://localhost:8000/docs/api
```

This documentation is automatically generated using Scramble and includes:
- All available endpoints
- Request/response schemas
- Validation rules
- Try-it-out functionality

## Environment Variables

### Required Variables

```env
# Application
APP_NAME=Laravel              # Application name
APP_ENV=local                 # Environment (local, staging, production)
APP_KEY=                      # Generated by `php artisan key:generate`
APP_DEBUG=true               # Debug mode (false in production)
APP_URL=http://localhost     # Application URL

# Database
DB_CONNECTION=mysql          # Database driver (mysql, pgsql, sqlite)
DB_HOST=127.0.0.1           # Database host
DB_PORT=3306                # Database port
DB_DATABASE=laravel         # Database name
DB_USERNAME=root            # Database username
DB_PASSWORD=                # Database password
```

### Optional Variables

```env
# Queue
QUEUE_CONNECTION=database    # Queue driver (sync, database, redis, sqs)

# Cache
CACHE_STORE=database        # Cache driver (file, database, redis)

# Session
SESSION_DRIVER=database     # Session storage (file, database, redis)
SESSION_LIFETIME=120        # Session lifetime in minutes

# Mail
MAIL_MAILER=log            # Mail driver (smtp, log, mailgun, etc.)
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_FROM_ADDRESS=hello@example.com

# Logging
LOG_CHANNEL=stack          # Log channel
LOG_LEVEL=debug           # Log level (debug, info, warning, error)

# API
API_VERSION=0.0.1         # API version for documentation
```

### Production-Specific

```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error

# Enable optimization
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## Project Structure

```
laravel12-api/
├── app/
│   ├── Console/          # Artisan commands (auto-registered)
│   ├── Data/            # Spatie Data DTOs
│   ├── Enums/           # PHP Enums (backed by strings)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/     # API controllers
│   │   ├── Requests/
│   │   │   └── Api/     # Form Request validation
│   │   └── Resources/
│   │       └── Api/     # API Resources & Collections
│   ├── Models/          # Eloquent models
│   ├── Providers/       # Service providers
│   └── Traits/          # Reusable traits (HasUserId, EnumArray)
├── bootstrap/
│   ├── app.php          # Application configuration (Laravel 12)
│   └── providers.php    # Service provider registration
├── config/              # Configuration files
├── database/
│   ├── factories/       # Model factories
│   ├── migrations/      # Database migrations
│   └── seeders/        # Database seeders
├── public/             # Public assets
├── resources/
│   ├── views/          # Blade templates
│   ├── css/            # Frontend CSS
│   └── js/             # Frontend JavaScript
├── routes/
│   ├── api.php         # API routes (prefixed with /api)
│   ├── web.php         # Web routes
│   └── console.php     # Console routes
├── storage/            # Application storage
├── tests/
│   ├── Feature/        # Feature tests
│   ├── Unit/          # Unit tests
│   └── Browser/       # Browser tests (Pest v4)
└── vendor/            # Composer dependencies
```

### Key Architectural Patterns

- **Laravel 12 Structure**: No `app/Http/Middleware/` or `app/Console/Kernel.php`
- **Data Layer**: Spatie Data objects for type-safe DTOs
- **API Resources**: Transform models to API responses
- **Form Requests**: Centralized validation logic
- **Traits**: Shared behaviors (e.g., `HasUserId` auto-assigns authenticated user)

## Development Environment Options

### Option 1: DDEV (Recommended for Docker)

This project includes DDEV configuration for containerized development.

```bash
# Start DDEV
ddev start

# Run composer commands
ddev composer install

# Run artisan commands
ddev artisan migrate
ddev artisan test

# Access the application
ddev launch
```

DDEV provides:
- PHP 8.4 environment
- MySQL/MariaDB database
- Automatic HTTPS
- MailHog for email testing
- Adminer for database management

### Option 2: Laravel Sail

Laravel Sail is available for Docker-based development.

```bash
# Install dependencies
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install

# Start Sail
./vendor/bin/sail up -d

# Run artisan commands
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan test

# Stop Sail
./vendor/bin/sail down
```

### Option 3: Traditional Local Setup

Requirements:
- PHP 8.4+
- Composer 2.9+
- MySQL/MariaDB
- Node.js & NPM

Follow the [Local Installation](#local-installation) section above.

## Authentication & Authorization

### Authentication

This project uses **Spatie Laravel Permission** for role and permission management.

#### Setting Up Roles & Permissions

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Create roles
$admin = Role::create(['name' => 'admin']);
$editor = Role::create(['name' => 'editor']);

// Create permissions
$editPosts = Permission::create(['name' => 'edit posts']);
$deletePosts = Permission::create(['name' => 'delete posts']);

// Assign permissions to roles
$admin->givePermissionTo(['edit posts', 'delete posts']);
$editor->givePermissionTo('edit posts');

// Assign role to user
$user->assignRole('admin');
```

#### Protecting Routes

```php
// In routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('posts', PostController::class);
});
```

#### Checking Permissions in Controllers

```php
public function update(Request $request, Post $post)
{
    $this->authorize('update', $post);
    // or
    if ($request->user()->can('edit posts')) {
        // Update post
    }
}
```

### API Authentication Setup

To implement API authentication:

1. **Install Laravel Sanctum** (if not already installed):
   ```bash
   composer require laravel/sanctum
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   php artisan migrate
   ```

2. **Generate API tokens**:
   ```php
   $token = $user->createToken('api-token')->plainTextToken;
   ```

3. **Use token in requests**:
   ```bash
   curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost:8000/api/posts
   ```

### HasUserId Trait

The `HasUserId` trait automatically assigns the authenticated user's ID when creating models:

```php
use App\Traits\HasUserId;

class Post extends Model
{
    use HasUserId;

    // user_id will be automatically set from Auth::user()
}
```

## Troubleshooting

### Common Issues

#### Vite Manifest Error

**Error:** `Unable to locate file in Vite manifest`

**Solution:**
```bash
npm run build
# or for development
npm run dev
```

#### Permission Denied Errors

**Error:** `Permission denied` when writing to storage/logs

**Solution:**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Database Connection Failed

**Error:** `SQLSTATE[HY000] [2002] Connection refused`

**Solution:**
1. Verify database is running
2. Check `.env` database credentials
3. Test connection: `php artisan db:show`

#### Queue Not Processing

**Issue:** Jobs remain in queue

**Solution:**
```bash
# Run queue worker
php artisan queue:work

# For development, use with timeout
php artisan queue:listen --tries=3

# Clear failed jobs
php artisan queue:flush
```

#### Class Not Found After Creating New File

**Solution:**
```bash
composer dump-autoload
```

#### Route Not Found

**Solution:**
```bash
php artisan route:clear
php artisan route:cache
php artisan route:list  # Verify route exists
```

#### Config Cached in Development

**Issue:** `.env` changes not taking effect

**Solution:**
```bash
php artisan config:clear
php artisan cache:clear
```

#### Tests Failing Due to Database

**Solution:**
```bash
# Use in-memory SQLite for tests
# In phpunit.xml or .env.testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

#### API Documentation Not Showing

**Solution:**
```bash
php artisan scramble:generate
php artisan route:clear
```

### Getting Help

- Check Laravel 12 documentation: https://laravel.com/docs/12.x
- Review Telescope for debugging: http://localhost:8000/telescope
- Enable debug mode: Set `APP_DEBUG=true` in `.env`
- Check logs: `storage/logs/laravel.log` or use `php artisan pail`

## Features

### Core API Features

- **RESTful API** - Standard REST conventions for all resources
- **Automatic OpenAPI Documentation** - Interactive docs at `/docs/api`
- **Advanced Querying** - Filter, sort, include relationships, select fields
- **Type-Safe DTOs** - Spatie Laravel Data for structured data transfer
- **Form Request Validation** - Centralized, reusable validation logic
- **API Resources** - Consistent response transformation
- **Role & Permission Management** - Spatie Laravel Permission integration

### Developer Experience

- **Laravel Telescope** - Debugging and monitoring dashboard
- **Laravel Debugbar** - Development debugging toolbar
- **IDE Helpers** - Full IDE autocompletion support
- **Laravel Pint** - Automatic code formatting
- **Pest v4 Testing** - Modern testing framework with browser testing
- **Hot Module Replacement** - Vite for fast frontend development
- **Real-time Logs** - Laravel Pail for log viewing

### Development Tools

- **Blueprint Integration** - Generate resources from YAML
- **Factory & Seeders** - Easy test data generation
- **Multiple Dev Environments** - DDEV, Sail, or traditional setup
- **Queue Management** - Background job processing
- **Database Migrations** - Version-controlled database changes

### Query Capabilities

Using Spatie Laravel Query Builder:

- **Includes** - Eager load relationships dynamically
- **Filters** - Filter by any model attribute
- **Sorting** - Sort by any field, ascending or descending
- **Field Selection** - Request only needed fields
- **Counts & Exists** - Include relationship counts

## Testing Strategy

### Testing Approach

This project follows a comprehensive testing strategy:

- **Feature Tests** - Test complete user flows and API endpoints
- **Unit Tests** - Test individual classes and methods in isolation
- **Browser Tests** - Test UI interactions (Pest v4)

### Writing Tests

Tests are located in:
- `tests/Feature/` - Feature tests for controllers, APIs
- `tests/Unit/` - Unit tests for models, services, utilities
- `tests/Browser/` - Browser-based tests

#### Example Feature Test

```php
<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can create a post', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/posts', [
        'title' => 'Test Post',
        'content' => 'Test content',
        'status' => 'draft',
        'user_id' => $user->id,
    ]);

    $response->assertCreated();
    expect(Post::count())->toBe(1);
});
```

#### Example Browser Test

```php
<?php

use function Pest\Laravel\visit;

test('can view post list', function () {
    $page = visit('/posts');

    $page->assertSee('Posts')
        ->assertNoJavascriptErrors();
});
```

### Running Tests

```bash
# All tests
php artisan test

# Specific test file
php artisan test tests/Feature/Http/Controllers/Api/PostControllerTest.php

# Filter by test name
php artisan test --filter=can_create_a_post

# With coverage
php artisan test --coverage

# Parallel testing
php artisan test --parallel

# Stop on failure
php artisan test --stop-on-failure
```

### Test Database

Tests use SQLite in-memory database by default for speed. Configure in `phpunit.xml`:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### Best Practices

- Use `RefreshDatabase` trait to reset database between tests
- Use model factories for creating test data
- Test happy paths, failure paths, and edge cases
- Use specific assertions (`assertCreated`, `assertOk`) instead of `assertStatus`
- Mock external services and APIs
- Keep tests fast and independent

## Deployment

### Pre-Deployment Checklist

Before deploying to production:

- [ ] Run all tests: `php artisan test`
- [ ] Format code: `vendor/bin/pint`
- [ ] Update dependencies: `composer update`
- [ ] Build assets: `npm run build`
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate secure `APP_KEY`
- [ ] Configure production database
- [ ] Set up queue workers
- [ ] Configure Redis for cache/sessions
- [ ] Review `.env` for sensitive data
- [ ] Set up SSL certificates
- [ ] Configure CORS if needed
- [ ] Set up monitoring and logging

### Deployment Steps

#### 1. Server Requirements

- PHP 8.4+
- Composer
- MySQL/PostgreSQL
- Redis (recommended)
- Node.js & NPM
- Supervisor (for queue workers)

#### 2. Initial Deployment

```bash
# Clone repository
git clone <repository-url>
cd laravel12-api

# Install dependencies
composer install --optimize-autoloader --no-dev

# Set up environment
cp .env.example .env
nano .env  # Configure production settings

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Build assets
npm ci
npm run build

# Optimize application
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 3. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 4. Configure Web Server

**Nginx Example:**

```nginx
server {
    listen 80;
    server_name api.example.com;
    root /var/www/laravel12-api/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### 5. Configure Queue Workers

Create Supervisor configuration at `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/laravel12-api/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/laravel12-api/storage/logs/worker.log
stopwaitsecs=3600
```

Reload Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

#### 6. Set Up Scheduled Tasks

Add to crontab:
```bash
* * * * * cd /var/www/laravel12-api && php artisan schedule:run >> /dev/null 2>&1
```

### Continuous Deployment

For automated deployments, consider:

- **Laravel Forge** - Managed Laravel hosting
- **Laravel Vapor** - Serverless deployment
- **Envoyer** - Zero-downtime deployment
- **GitHub Actions** - CI/CD pipeline
- **GitLab CI/CD** - Automated deployment

### Zero-Downtime Deployment

```bash
# Using symlinks
ln -nfs /var/www/releases/new /var/www/current

# Reload PHP-FPM
sudo systemctl reload php8.4-fpm

# Restart queue workers
php artisan queue:restart
```

### Post-Deployment

```bash
# Verify deployment
php artisan about

# Check health
curl https://api.example.com/up

# Monitor logs
tail -f storage/logs/laravel.log
```

## Contributing

We welcome contributions to this project! Here's how you can help:

### Getting Started

1. Fork the repository
2. Clone your fork: `git clone https://github.com/your-username/laravel12-api.git`
3. Create a feature branch: `git checkout -b feature/your-feature-name`
4. Install dependencies: `composer install && npm install`
5. Set up environment: `cp .env.example .env && php artisan key:generate`

### Development Guidelines

#### Code Standards

- Follow PSR-12 coding standards
- Run Laravel Pint before committing: `vendor/bin/pint --dirty`
- Use PHP 8.4 features (constructor property promotion, readonly, etc.)
- Add type hints to all methods and properties
- Write PHPDoc blocks for complex logic

#### Writing Code

- Follow existing code patterns and conventions
- Use Eloquent relationships instead of raw queries
- Create Form Requests for validation
- Return API Resources from controllers
- Use meaningful variable and method names
- Keep methods focused and single-purpose

#### Testing Requirements

- Write tests for all new features
- Maintain or improve test coverage
- All tests must pass before merging
- Use factories for test data
- Test happy paths, error cases, and edge cases

Run tests:
```bash
php artisan test
vendor/bin/pint --dirty
```

#### Commit Messages

Follow the commit message format:
```
type: brief description

Longer explanation if needed
```

Types: `feat`, `fix`, `refactor`, `test`, `docs`, `style`, `chore`

### Submitting Changes

1. Ensure all tests pass: `php artisan test`
2. Format code: `vendor/bin/pint --dirty`
3. Commit your changes with clear messages
4. Push to your fork
5. Open a Pull Request to the `develop` branch

### Pull Request Guidelines

Your PR should:
- Have a clear title and description
- Reference any related issues
- Include tests for new functionality
- Pass all CI checks
- Update documentation if needed
- Follow the project's code style

### Code Review Process

- PRs require at least one approval
- Address review feedback promptly
- Keep PRs focused and reasonably sized
- Squash commits before merging if requested

### Reporting Issues

When reporting bugs:
- Use a clear, descriptive title
- Describe steps to reproduce
- Include error messages and logs
- Specify your environment (PHP version, OS, etc.)
- Provide code samples if relevant

### Feature Requests

When suggesting features:
- Explain the use case
- Describe the expected behavior
- Discuss potential implementation approaches
- Consider backward compatibility

### Questions?

- Check existing issues and documentation first
- Open a discussion for general questions
- Join our community chat (if available)

Thank you for contributing!

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

### MIT License

Copyright (c) 2025 Laravel 12 REST API Starter

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.