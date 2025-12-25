# Testing Strategy

This document outlines the comprehensive testing approach for our modular monolith architecture.

## Table of Contents

1. [Testing Philosophy](#testing-philosophy)
2. [Test Types](#test-types)
3. [Testing Tools](#testing-tools)
4. [Test Organization](#test-organization)
5. [Architecture Tests](#architecture-tests)
6. [Feature Tests](#feature-tests)
7. [Unit Tests](#unit-tests)
8. [Running Tests](#running-tests)
9. [Code Coverage](#code-coverage)
10. [Best Practices](#best-practices)

## Testing Philosophy

Our testing strategy follows these core principles:

- **Test what matters**: Focus on behavior, not implementation details
- **Test boundaries**: Ensure architectural boundaries are enforced
- **Test workflows**: Validate complete user workflows, not just individual methods
- **Fast feedback**: Keep tests fast to encourage frequent execution
- **Maintainable**: Write clear, readable tests that serve as documentation

## Test Types

### 1. Architecture Tests
Architecture tests ensure that our modular monolith boundaries are respected and coding conventions are followed.

**Location**: 
- Global: `tests/Architecture/`
- Per Module: `app-modules/{module}/tests/Feature/ArchTest.php` and `app-modules/{module}/tests/Unit/ArchTest.php`

**What they test**:
- Module isolation and boundaries
- Dependency rules
- Naming conventions
- Code structure compliance
- No debugging statements in production code
- Strict types declaration

### 2. Feature Tests
Feature tests validate complete user workflows and HTTP interactions.

**Location**:
- Core: `tests/Feature/`
- Per Module: `app-modules/{module}/tests/Feature/`

**What they test**:
- HTTP routes and responses
- CRUD operations
- Form validation
- Authentication and authorization
- View rendering
- Session management
- Database interactions

### 3. Unit Tests
Unit tests validate individual components in isolation.

**Location**:
- Core: `tests/Unit/`
- Per Module: `app-modules/{module}/tests/Unit/`

**What they test**:
- Actions
- Services
- Repositories
- DTOs
- Value Objects
- Business logic
- Calculations and transformations

## Testing Tools

### Pest PHP
We use [Pest](https://pestphp.com/) as our testing framework.

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run with filter
php artisan test --filter="test_name"

# Run tests in parallel (faster)
php artisan test --parallel
```

### RefreshDatabase
Most feature and unit tests use the `RefreshDatabase` trait to ensure a clean database state:

```php
uses(TestCase::class, RefreshDatabase::class);
```

### Factories
We use model factories to create test data:

```php
$client = Client::factory()->create([
    'name' => 'Test Client',
    'email' => 'test@example.com',
]);

// Create multiple
$clients = Client::factory()->count(5)->create();
```

## Test Organization

### Module Test Structure
Each module follows this test structure:

```
app-modules/{module}/
├── tests/
│   ├── Feature/
│   │   ├── ArchTest.php              # Module boundary tests
│   │   └── {Module}CrudTest.php      # HTTP/CRUD tests
│   └── Unit/
│       ├── ArchTest.php              # Module structure tests
│       ├── Actions/
│       │   └── {Action}Test.php      # Action tests
│       ├── Services/
│       │   └── {Service}Test.php     # Service tests
│       └── Repositories/
│           └── {Repository}Test.php  # Repository tests
```

### Core Test Structure
Core application tests:

```
tests/
├── Architecture/
│   └── ModularArchTest.php          # Global architecture tests
├── Feature/
│   ├── Auth/
│   │   └── LoginTest.php            # Authentication tests
│   ├── Console/
│   │   └── MakeModuleCommandTest.php # Command tests
│   └── SmokeTest.php                # Smoke tests
└── Unit/
    └── Providers/
        └── ModuleServiceProviderTest.php
```

## Architecture Tests

### Global Architecture Tests

Located in `tests/Architecture/ModularArchTest.php`, these tests enforce:

1. **Naming Conventions**
   - Controllers end with `Controller`
   - Actions end with `Action`
   - Services end with `Service`
   - etc.

2. **Namespace Structure**
   - Actions in `Actions` namespace
   - Services in `Services` namespace
   - Events in `Events` namespace
   - etc.

3. **Dependency Rules**
   - Controllers don't use Models directly
   - Modules don't use core `App\Models` (except `User`)
   - No debugging functions (`dd`, `dump`, `var_dump`)
   - No direct `env()` calls outside config

4. **Code Quality**
   - Strict types declaration
   - Proper return types
   - Interface contracts

### Module Boundary Tests

Each module has boundary tests in `app-modules/{module}/tests/Feature/ArchTest.php`:

```php
arch()
    ->expect('AppModules\Invoice\src')
    ->not->toBeUsedIn('AppModules\Client\src')
    ->not->toBeUsedIn('AppModules\Payment\src')
    ->ignoring([
        'AppModules\Invoice\src\Contracts',
        'AppModules\Invoice\src\DataTransferObjects',
        'AppModules\Invoice\src\Events',
        'AppModules\Invoice\src\Enums',
        'AppModules\Invoice\src\Exceptions',
    ]);
```

This ensures:
- Modules don't directly depend on other module internals
- Only public API (Contracts, DTOs, Events, Enums) can be shared
- Module boundaries are enforced at compile time

## Feature Tests

### HTTP CRUD Tests Example

```php
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('can display the index page', function () {
    Product::factory()->count(3)->create();

    get(route('product::index'))
        ->assertSuccessful()
        ->assertViewIs('product::index')
        ->assertSee('Products');
});

it('can create a product', function () {
    post(route('product::store'), [
        'name' => 'Test Product',
        'price' => 99.99,
    ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('products', [
        'name' => 'Test Product',
    ]);
});
```

### Validation Tests

Always test validation rules:

```php
it('validates required fields', function () {
    post(route('product::store'), [])
        ->assertSessionHasErrors(['name', 'price']);
});

it('validates price is non-negative', function () {
    post(route('product::store'), [
        'name' => 'Test',
        'price' => -10,
    ])
        ->assertSessionHasErrors(['price']);
});
```

## Unit Tests

### Action Tests Example

```php
it('calculates invoice totals correctly', function () {
    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create(['client_id' => $client->id]);

    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'quantity' => 2,
        'unit_price' => 100.00,
        'tax_rate' => 20.0,
    ]);

    $action = app(CalculateInvoiceTotalsAction::class);
    $invoice = $action->handle($invoice->fresh());

    expect((float) $invoice->subtotal)->toBe(200.0);
    expect((float) $invoice->tax_amount)->toBe(40.0);
    expect((float) $invoice->total)->toBe(240.0);
});
```

### Service Tests Example

```php
it('can create a client', function () {
    $service = app(ClientService::class);

    $dto = new ClientDTO(
        name: 'Test Client',
        email: 'test@example.com',
        // ... other fields
    );

    $client = $service->create($dto);

    expect($client)
        ->toBeInstanceOf(Client::class)
        ->name->toBe('Test Client');

    $this->assertDatabaseHas('clients', [
        'email' => 'test@example.com',
    ]);
});
```

## Running Tests

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suite

```bash
# Feature tests only
php artisan test tests/Feature

# Unit tests only
php artisan test tests/Unit

# Architecture tests only
php artisan test tests/Architecture

# Module tests
php artisan test app-modules/invoice/tests
```

### Run with Filter

```bash
# Run tests matching name
php artisan test --filter="can create a product"

# Run specific test file
php artisan test tests/Feature/Auth/LoginTest.php
```

### Run Tests in Parallel

```bash
# Much faster for large test suites
php artisan test --parallel
```

### Run with Coverage

```bash
# Generate HTML coverage report
php artisan test --coverage --coverage-html=coverage

# Coverage with minimum threshold
php artisan test --coverage --min=80
```

## Code Coverage

We aim for:
- **Overall**: 80%+ coverage
- **Actions**: 90%+ coverage
- **Services**: 85%+ coverage
- **Controllers**: 70%+ coverage (mostly feature tests)

### Viewing Coverage

```bash
# Generate coverage report
php artisan test --coverage-html=coverage

# Open in browser
open coverage/index.html
```

## Best Practices

### 1. Test Naming

Use descriptive test names:

```php
// ✅ Good
it('can create a product with valid data')
it('validates email is required when creating client')
it('prevents finalizing invoice without items')

// ❌ Bad
it('test_create')
it('validation')
```

### 2. Arrange-Act-Assert Pattern

Structure tests clearly:

```php
it('can calculate invoice total', function () {
    // Arrange
    $invoice = Invoice::factory()->create();
    InvoiceItem::factory()->create(['invoice_id' => $invoice->id]);

    // Act
    $action = app(CalculateInvoiceTotalsAction::class);
    $result = $action->handle($invoice);

    // Assert
    expect($result->total)->toBeGreaterThan(0);
});
```

### 3. Test One Thing

Each test should verify one behavior:

```php
// ✅ Good
it('validates name is required')
it('validates email is required')
it('validates email format is valid')

// ❌ Bad
it('validates all fields') // Too broad
```

### 4. Use Factories

Always use factories instead of manual creation:

```php
// ✅ Good
$client = Client::factory()->create(['name' => 'Test']);

// ❌ Bad
$client = new Client();
$client->name = 'Test';
$client->save();
```

### 5. Test Edge Cases

Don't just test happy paths:

```php
it('handles empty invoice items')
it('handles zero tax rate')
it('handles maximum discount')
it('handles negative quantities (validation)')
```

### 6. Keep Tests Fast

- Use `RefreshDatabase` instead of `DatabaseMigrations`
- Don't test third-party code
- Mock external services
- Use `$this->artisan()` instead of `Artisan::call()`

### 7. Avoid Test Interdependence

Tests should be independent:

```php
// ✅ Good - Each test is independent
it('can create user', function () {
    $user = User::factory()->create();
    expect($user)->toBeInstanceOf(User::class);
});

it('can update user', function () {
    $user = User::factory()->create(); // Create fresh
    // ... test update
});

// ❌ Bad - Tests depend on each other
```

### 8. Test Failure Paths

```php
it('prevents finalizing draft invoice without items', function () {
    $invoice = Invoice::factory()->create();

    $action = app(FinalizeInvoiceAction::class);

    expect(fn () => $action->handle($invoice))
        ->toThrow(DomainException::class);
});
```

### 9. Use Datasets for Similar Tests

```php
it('validates email format', function (string $email) {
    post(route('client::store'), ['email' => $email])
        ->assertSessionHasErrors(['email']);
})->with([
    'invalid-email',
    'test@',
    '@example.com',
    'test @example.com',
]);
```

### 10. Clean Up After Tests

```php
afterEach(function () {
    // Clean up files, directories, etc.
    if (File::exists($path)) {
        File::deleteDirectory($path);
    }
});
```

## Continuous Integration

Tests should run on every commit and pull request:

```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.5'
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test --parallel
      - name: Check Architecture
        run: php artisan test tests/Architecture
```

## Summary

A comprehensive test suite:
- ✅ Enforces architectural boundaries
- ✅ Validates business logic
- ✅ Ensures code quality
- ✅ Serves as living documentation
- ✅ Prevents regressions
- ✅ Enables confident refactoring

**Remember**: Tests are not a burden—they're an investment that pays dividends in code quality and maintainability.
