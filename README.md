# VIP Tutors API

## Architecture

This API follows a layered architecture pattern:

### Repository Pattern
- Located in `app/Repositories/`
- Handles data access and persistence
- Implements interfaces defined in `app/Repositories/Interfaces/`
- Example: `TaskRepository` for task-related database operations

### Service Layer
- Located in `app/Services/`
- Contains business logic
- Uses repositories for data access
- Example: `TaskService` for task management operations

### Controllers
- Located in `app/Http/Controllers/`
- Handles HTTP requests and responses
- Uses services for business logic
- Implements API documentation using Swagger/OpenAPI

## Testing

### Feature Tests
- Located in `tests/Feature/`
- Tests complete features end-to-end
- Uses `RefreshDatabase` trait for clean test state
- Example: `TaskReorderTest` for task reordering functionality

### Unit Tests
- Located in `tests/Unit/`
- Tests individual components in isolation
- Mocks dependencies using Laravel's testing tools

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/TaskReorderTest.php

# Run tests with coverage
php artisan test --coverage
```

## API Documentation

API documentation is available at `/api/documentation` when running the application locally.

### Generating Documentation
```bash
php artisan l5-swagger:generate
```

## Environment Setup

1. Copy `.env.example` to `.env`
2. Configure your database settings
3. Set `L5_SWAGGER_CONST_HOST` for API documentation
4. Run migrations: `php artisan migrate`
5. Start the server: `php artisan serve`

## Development

### Code Style
- Follows PSR-12 standards
- Uses Laravel Pint for code style enforcement

### Git Workflow
1. Create feature branch from `main`
2. Write tests first (TDD approach)
3. Implement feature
4. Submit pull request

## Security

- Uses Laravel Sanctum for API authentication
- Implements JWT tokens for secure communication
- All endpoints require authentication unless specified otherwise
