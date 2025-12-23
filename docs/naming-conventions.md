# Conventions de Nommage

Ce document décrit les conventions de nommage utilisées dans le projet.

## Modules

- **Nom du module** : PascalCase (ex: `Invoice`, `Product`, `Client`)
- **Dossier** : PascalCase (ex: `app-modules/Invoice/`)
- **Namespace** : `AppModules\{ModuleName}`

## Classes

### Actions
- **Name**: PascalCase with `Action` suffix (e.g., `CreateInvoiceAction`)
- **Namespace**: `AppModules\{Module}\src\Actions`
- **Main method**: `handle()`

### Services
- **Name**: PascalCase with `Service` suffix (e.g., `InvoiceService`)
- **Namespace**: `AppModules\{Module}\src\Services`

### Repositories
- **Name**: PascalCase with `Repository` suffix (e.g., `InvoiceRepository`)
- **Namespace**: `AppModules\{Module}\src\Repositories`

### DTOs
- **Name**: PascalCase with `Data` suffix (e.g., `InvoiceData`)
- **Namespace**: `AppModules\{Module}\src\DataTransferObjects`
- **Properties**: `readonly` and `public`

### Value Objects
- **Name**: PascalCase (e.g., `Money`, `Email`)
- **Namespace**: `AppModules\Shared\src\ValueObjects`
- **Class**: `final readonly class`

### Events
- **Name**: PascalCase matching the action (e.g., `InvoiceCreated`)
- **Namespace**: `AppModules\{Module}\src\Events`

### Exceptions
- **Name**: PascalCase with `Exception` suffix (e.g., `InvoiceNotFoundException`, `InvalidInvoiceStatusException`)
- **Namespace**: `AppModules\{Module}\src\Exceptions`
- **Extends**: `Exception` or Laravel exception classes (e.g., `ModelNotFoundException`)

### Jobs
- **Name**: PascalCase with `Job` suffix (e.g., `GenerateInvoicePDFJob`)
- **Namespace**: `AppModules\{Module}\src\Jobs`

## Routes

- **Prefix**: Module name in lowercase (e.g., `invoices`)
- **Route name**: `{module}::{action}` (e.g., `invoice::index`, `invoice::show`)

## Blade Views

- **Location**: `resources/views/modules/{module}/`
- **Namespace**: Module name in lowercase (e.g., `invoice::`)
- **Files**: kebab-case (e.g., `create-invoice.blade.php`)

## Blade Components

- **Location**: `resources/views/components/{module}/`
- **Namespace**: `{module}::` or `shared::`
- **Component name**: kebab-case (e.g., `invoice-status.blade.php`)
- **Usage**: `<x-invoice::invoice-status />`

## Tests

- **Unit tests**: `{ClassName}Test.php` in `tests/unit/`
- **Feature tests**: `{FeatureName}Test.php` in `tests/feature/`
- **Namespace**: `AppModules\{Module}\tests\{type}`

## Migrations

- **Format**: `{YYYY_MM_DD}_{HHMMSS}_create_{table_name}_table.php`
- **Location**: `database/migrations/`

## Examples

### Action
```php
namespace AppModules\Invoice\src\Actions;

class CreateInvoiceAction
{
    public function handle(InvoiceData $data): Invoice
    {
        // ...
    }
}
```

### Service
```php
namespace AppModules\Invoice\src\Services;

class InvoiceService
{
    public function __construct(
        private CreateInvoiceAction $createInvoice
    ) {}
}
```

### Route
```php
Route::prefix('invoices')->name('invoice::')->group(function () {
    Route::get('/', [InvoiceController::class, 'index'])->name('index');
});
```

### View
```blade
@extends('layouts.app')

@section('content')
    <x-invoice::invoice-status :status="$invoice->status" />
@endsection
```
