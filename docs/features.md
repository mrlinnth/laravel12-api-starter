# Features

## Core API Features

- **RESTful API** - Standard REST conventions for all resources (Posts, Comments, Tags)
- **BaseApiController Pattern** - Unified base controller with automatic query building and pagination
- **ApiResponse Trait** - Standardized JSON responses with consistent error handling
- **Automatic OpenAPI Documentation** - Interactive docs with "Try It" feature at `/docs/api`
- **Advanced Querying** - Filter, sort, include relationships, select specific fields
- **Type-Safe DTOs** - Spatie Laravel Data for automatic validation and data transfer
- **Data Object Validation** - Automatic validation from type-hinted properties
- **API Resources** - Consistent response transformation
- **Role & Permission Management** - Spatie Laravel Permission integration

## Developer Experience

- **Custom Blueprint Generator** - ApiControllerGenerator for automatic BaseApiController creation
- **Smart Code Generation** - Auto-detects filters, sorts, and includes from model schema
- **Laravel Telescope** - Debugging and monitoring dashboard at `/telescope`
- **Laravel Debugbar** - Development debugging toolbar
- **IDE Helpers** - Full IDE autocompletion support
- **Laravel Pint** - Automatic code formatting (PSR-12)
- **Pest v4 Testing** - Modern testing framework with browser testing
- **Hot Module Replacement** - Vite for fast frontend development
- **Real-time Logs** - Laravel Pail for log viewing
- **Blueprint Integration** - Generate complete resources from YAML definitions

## Development Tools

- **Multiple Dev Environments** - DDEV, Laravel Sail, or traditional setup
- **Factory & Seeders** - Easy test data generation
- **Queue Management** - Background job processing
- **Database Migrations** - Version-controlled database schema

## Query Capabilities

Using Spatie Laravel Query Builder:

- **Includes** - Dynamically eager load relationships (`?include=user,comments`)
- **Filters** - Filter by any attribute (`?filter[status]=published`)
- **Sorting** - Sort by any field (`?sort=-created_at`)
- **Field Selection** - Request only needed fields (`?fields=id,title`)
- **Counts & Exists** - Include relationship counts
