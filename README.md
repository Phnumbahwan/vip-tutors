# VIP Tutors API

## 🛠️ Setup Instructions For Backend

### 1️⃣ Copy `.env` File

Duplicate the environment file provided:

```bash
cp .env.example .env
```

### 2️⃣ Build Docker Containers

Build the application containers:

```bash
docker-compose build
```

### 3️⃣ Start Docker Containers

Start the containers in your terminal:

```bash
docker-compose up
```

Or start in detached mode:

```bash
docker-compose up -d
```

### 4️⃣ Access PHP Container

Enter the running PHP container:

```bash
docker-compose exec php-fpm bash
```

### 5️⃣ Navigate to Laravel Project Directory

Once inside the container, change directory to the Laravel project:

```bash
cd /var/www/html
```

### 6️⃣ Install PHP Dependencies

Install Laravel's PHP dependencies using Composer:

```bash
composer install
```

### 7️⃣ Generate Laravel Application Key

Generate a new app key for Laravel:

```bash
php artisan key:generate
```

### 8️⃣ Run Migrations with Seeding

Migrate the database and seed it with initial data:

```bash
php artisan migrate --seed
```

Your Backend API is available here: 

```
http://localhost
```

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
- Uses Pest for unit testing
- Located in `tests/Feature/`
- Tests individual components in isolation
- Mocks dependencies using Laravel's testing tools

### Running Tests
```bash
# Run all tests
php artisan test or ./vendor/bin/pest

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

### You can use Postman. Here's the look from it.
![image](https://github.com/user-attachments/assets/01998e74-51bd-4718-83f9-67b5e8c9d6ee)


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

## 🛠️ Setup Instructions For Frontend
1. npm install
2. npm run dev
