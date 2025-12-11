# Testing Strategy

## Testing Approach

This project follows a comprehensive testing strategy:

- **Feature Tests** - Test complete user flows and API endpoints
- **Unit Tests** - Test individual classes and methods in isolation
- **Browser Tests** - Test UI interactions using Pest v4

## Test Locations

- `tests/Feature/` - Feature tests for controllers, APIs
- `tests/Unit/` - Unit tests for models, services, utilities
- `tests/Browser/` - Browser-based tests

## Example Feature Test

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

## Example Browser Test

```php
<?php

use function Pest\Laravel\visit;

test('can view post list', function () {
    $page = visit('/posts');

    $page->assertSee('Posts')
        ->assertNoJavascriptErrors();
});
```

## Running Tests

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

## Test Database

Tests use SQLite in-memory database by default for speed. Configure in `phpunit.xml`:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## Testing Best Practices

- Use `RefreshDatabase` trait to reset database between tests
- Use model factories for creating test data
- Test happy paths, failure paths, and edge cases
- Use specific assertions (`assertCreated`, `assertOk`) instead of `assertStatus`
- Mock external services and APIs
- Keep tests fast and independent
