# Authentication & Authorization

## Spatie Laravel Permission

This project uses **Spatie Laravel Permission** for role and permission management.

### Setting Up Roles & Permissions

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

### Protecting Routes

```php
// In routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('posts', PostController::class);
});
```

### Checking Permissions in Controllers

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

## API Authentication Setup

To implement API authentication with Laravel Sanctum:

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

## HasUserId Trait

The `HasUserId` trait automatically assigns the authenticated user's ID when creating models:

```php
use App\Traits\HasUserId;

class Post extends Model
{
    use HasUserId;

    // user_id will be automatically set from Auth::user()
}
```
