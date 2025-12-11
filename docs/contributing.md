# Contributing

We welcome contributions to this project! Here's how you can help:

## Getting Started

1. Fork the repository
2. Clone your fork: `git clone https://github.com/your-username/laravel12-api.git`
3. Create a feature branch: `git checkout -b feature/your-feature-name`
4. Install dependencies: `composer install && npm install`
5. Set up environment: `cp .env.example .env && php artisan key:generate`

## Development Guidelines

### Code Standards

- Follow PSR-12 coding standards
- Run Laravel Pint before committing: `vendor/bin/pint --dirty`
- Use PHP 8.4 features (constructor property promotion, readonly, etc.)
- Add type hints to all methods and properties
- Write PHPDoc blocks for complex logic

### Writing Code

- Follow existing code patterns and conventions
- Use Eloquent relationships instead of raw queries
- Create Form Requests for validation
- Return API Resources from controllers
- Use meaningful variable and method names
- Keep methods focused and single-purpose

### Testing Requirements

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

## Commit Message Guidelines

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

## Submitting Changes

1. Ensure all tests pass: `php artisan test`
2. Format code: `vendor/bin/pint --dirty`
3. Commit your changes with clear messages
4. Push to your fork
5. Open a Pull Request to the `develop` branch

## Pull Request Guidelines

Your PR should:
- Have a clear title and description
- Reference any related issues
- Include tests for new functionality
- Pass all CI checks
- Update documentation if needed
- Follow the project's code style

## Code Review Process

- PRs require at least one approval
- Address review feedback promptly
- Keep PRs focused and reasonably sized
- Squash commits before merging if requested

## Branch Naming

- `feature/` - New features (e.g., `feature/add-post-filtering`)
- `fix/` - Bug fixes (e.g., `fix/n-plus-one-posts`)
- `refactor/` - Code improvements (e.g., `refactor/optimize-queries`)
- `test/` - Test additions (e.g., `test/post-controller`)

## Current Branch Structure

- **main** - Production-ready code
- **develop** - Development branch (current working branch)

## Reporting Issues

When reporting bugs:
- Use a clear, descriptive title
- Describe steps to reproduce
- Include error messages and logs
- Specify your environment (PHP version, OS, etc.)
- Provide code samples if relevant

## Feature Requests

When suggesting features:
- Explain the use case
- Describe the expected behavior
- Discuss potential implementation approaches
- Consider backward compatibility

## Questions?

- Check existing issues and documentation first
- Open a discussion for general questions
- Join our community chat (if available)

Thank you for contributing!
