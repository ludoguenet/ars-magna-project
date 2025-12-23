# PHP Enums

PHP Enums are used to represent fixed sets of values in your domain. They live in `src/Enums/` and are considered **public APIs** that can be used by other modules.

## Location

Enums are located in `src/Enums/` within each module:

```
app-modules/invoice/src/Enums/
└── InvoiceStatus.php
```

## Example

```php
<?php

namespace AppModules\Invoice\src\Enums;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Brouillon',
            self::SENT => 'Envoyée',
            self::PAID => 'Payée',
            self::OVERDUE => 'En retard',
            self::CANCELLED => 'Annulée',
        };
    }

    public function canBeEdited(): bool
    {
        return $this === self::DRAFT;
    }
}
```

## Using Enums in Models

Enums can be used as Eloquent casts:

```php
// src/Models/Invoice.php
protected $casts = [
    'status' => InvoiceStatus::class,
];

// Usage
$invoice->status = InvoiceStatus::PAID;
$invoice->status->label(); // "Payée"
```

## Public API

Enums are considered **public APIs** and can be used by other modules:

```php
// In another module
use AppModules\Invoice\src\Enums\InvoiceStatus;

$invoices = Invoice::where('status', InvoiceStatus::PAID)->get();
```

## Architecture Testing

Enums are ignored in architecture tests since they're public APIs:

```php
test('invoice module boundaries are enforced')
    ->expect('AppModules\Invoice')
    ->toOnlyUse(['AppModules\Invoice', 'Illuminate'])
    ->ignoring('AppModules\Invoice\src\Enums');
```

## Related Documentation

- [Module Structure](./02-module-structure.md) - How modules are organized
- [Enforcing Boundaries](./04-enforcing-boundaries.md) - Architecture testing
