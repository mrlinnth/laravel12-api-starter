# API Usage & Examples

## Available Endpoints

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

## Example Requests

### Get All Posts

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

### Create a New Post

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

## Advanced Query Parameters

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

## API Documentation

Interactive API documentation with "Try It" feature is available at:
```
http://localhost:8000/docs/api
```

This documentation is automatically generated using Scramble and includes:
- All available endpoints
- Request/response schemas
- Validation rules
- Try-it-out functionality
