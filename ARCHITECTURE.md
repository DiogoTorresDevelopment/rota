# Clean Architecture Implementation - Modular Structure

## Overview

This project has been restructured to follow Clean Architecture principles, SOLID design patterns, and modular organization. The codebase is now organized by modules (Drivers and Passengers) with clear separation of concerns.

## Architecture Structure

### Module Organization

```
app/
├── Modules/
│   ├── Drivers/
│   │   ├── Controllers/
│   │   │   ├── DriverController.php
│   │   │   └── DriverDocumentController.php
│   │   └── Services/
│   │       ├── DriverService.php
│   │       └── DriverDocumentService.php
│   ├── Passengers/
│   │   ├── Controllers/
│   │   │   └── PassengerController.php
│   │   └── Services/
│   │       └── PassengerService.php
│   └── Shared/
│       └── [Common functionality across modules]
├── Repositories/
│   ├── Contracts/
│   │   ├── DriverRepositoryInterface.php
│   │   └── PassengerRepositoryInterface.php
│   └── Eloquent/
│       ├── DriverRepository.php
│       └── PassengerRepository.php
└── Providers/
    └── RepositoryServiceProvider.php
```

### Routes Organization

```
routes/
├── modules/
│   ├── drivers/
│   │   └── web.php
│   └── passengers/
│       └── web.php
├── web.php (main routes)
└── api.php
```

## Design Patterns Implemented

### 1. Repository Pattern
- **Interface Contracts**: Define contracts for data access operations
- **Implementation**: Eloquent implementations of repository interfaces
- **Benefits**: Decouples business logic from data access, makes testing easier

Example:
```php
// Contract
interface DriverRepositoryInterface {
    public function all(): Collection;
    public function find(int $id): ?Driver;
    // ...
}

// Implementation
class DriverRepository implements DriverRepositoryInterface {
    public function all(): Collection {
        return Driver::all();
    }
    // ...
}
```

### 2. Service Layer Pattern
- **Single Responsibility**: Each service handles one domain
- **Business Logic**: Contains all business rules and data processing
- **Clean Controllers**: Controllers become thin and focused on HTTP concerns

Example:
```php
class DriverService {
    public function __construct(
        private DriverRepositoryInterface $driverRepository
    ) {}

    public function createDriver(array $data): Driver {
        $data = $this->prepareDriverData($data);
        return $this->driverRepository->create($data);
    }
}
```

### 3. Dependency Injection
- **Constructor Injection**: Dependencies injected through constructors
- **Service Container**: Laravel's container manages dependencies
- **Interface Binding**: Interfaces bound to implementations via service provider

Example:
```php
// Service Provider
$this->app->bind(
    DriverRepositoryInterface::class,
    DriverRepository::class
);

// Controller
public function __construct(
    private DriverService $driverService
) {}
```

## SOLID Principles Implementation

### Single Responsibility Principle (SRP)
- **Controllers**: Only handle HTTP requests/responses
- **Services**: Handle business logic for specific domains
- **Repositories**: Only handle data access operations
- **Separate Document Service**: Driver documents handled by dedicated service

### Open/Closed Principle (OCP)
- **Interfaces**: Code open for extension via interfaces
- **Repository Pattern**: New storage implementations can be added without changing business logic

### Liskov Substitution Principle (LSP)
- **Repository Interfaces**: Any implementation can replace another
- **Service Contracts**: Services can be extended or replaced

### Interface Segregation Principle (ISP)
- **Focused Interfaces**: Repositories have specific, focused contracts
- **No Fat Interfaces**: Each interface serves a specific purpose

### Dependency Inversion Principle (DIP)
- **Depend on Abstractions**: Services depend on interfaces, not concrete classes
- **Inject Dependencies**: All dependencies injected, not instantiated

## Module Features

### Drivers Module
- **CRUD Operations**: Full driver management
- **Document Management**: Separate service for handling driver documents
- **File Upload**: Secure document upload and storage
- **Validation**: Comprehensive form validation

### Passengers Module
- **CRUD Operations**: Full passenger management
- **Data Cleaning**: Automatic CPF and phone number cleaning
- **Validation**: Business rule validation
- **Status Management**: Active/inactive status control

## Benefits of This Architecture

### 1. Maintainability
- **Clear Structure**: Easy to locate and modify code
- **Separation of Concerns**: Each class has a single responsibility
- **Modular Design**: Changes in one module don't affect others

### 2. Testability
- **Unit Testing**: Services can be tested in isolation
- **Mocking**: Dependencies can be easily mocked
- **Test Coverage**: Better test coverage with focused units

### 3. Scalability
- **Module Addition**: New modules can be added easily
- **Feature Extension**: New features follow established patterns
- **Team Development**: Multiple developers can work on different modules

### 4. Code Reusability
- **Service Reuse**: Services can be used by different controllers
- **Repository Reuse**: Repositories can be used across the application
- **Shared Components**: Common functionality in shared modules

## Testing Strategy

### Unit Tests
- **Service Testing**: Business logic tested in isolation
- **Repository Testing**: Data access logic tested
- **Mock Dependencies**: External dependencies mocked

Example test structure:
```php
class DriverServiceTest extends TestCase {
    protected function setUp(): void {
        $this->driverRepository = Mockery::mock(DriverRepositoryInterface::class);
        $this->driverService = new DriverService($this->driverRepository);
    }
    
    public function test_can_create_driver() {
        // Test implementation
    }
}
```

## Usage Examples

### Creating a Driver
```php
// Controller
$driver = $this->driverService->createDriver($request->validated());

// Service handles business logic
private function prepareDriverData(array $data): array {
    // Password hashing, default values, etc.
}
```

### Querying Data
```php
// Service
$drivers = $this->driverRepository->getActiveDrivers();

// Repository
public function getActiveDrivers(): Collection {
    return Driver::where('status', 'active')->get();
}
```

## Future Enhancements

1. **Add More Modules**: Trucks, Routes, Deliveries modules
2. **Event System**: Domain events for cross-module communication
3. **CQRS**: Command Query Responsibility Segregation for complex operations
4. **API Resources**: Dedicated API resource classes for data transformation
5. **Caching Layer**: Repository caching for performance optimization

## Migration Guide

### For Existing Code
1. **Identify Modules**: Group related functionality
2. **Extract Services**: Move business logic from controllers to services
3. **Create Repositories**: Abstract data access operations
4. **Update Routes**: Move to module-specific route files
5. **Update Tests**: Create focused unit tests

### For New Features
1. **Choose Module**: Determine which module the feature belongs to
2. **Follow Patterns**: Use established service and repository patterns
3. **Add Tests**: Create comprehensive unit tests
4. **Update Documentation**: Document new features and changes