# Custom Exceptions

Custom exceptions are part of a module's **public API** and can be used by other modules to handle domain-specific errors.

## Location

Exceptions live in `src/Exceptions/` within each module:

```
app-modules/
├── {module}/
│   ├── src/
│   │   ├── Exceptions/
│   │   │   ├── InvoiceNotFoundException.php
│   │   │   └── InvalidInvoiceStatusException.php
```

## Naming Convention

- **Name**: PascalCase with `Exception` suffix
- **Namespace**: `AppModules\{Module}\src\Exceptions`
- **Examples**:
  - `InvoiceNotFoundException`
  - `InvalidInvoiceStatusException`
  - `InvoiceAlreadyFinalizedException`

## Creating Exceptions

### Basic Exception

```php
<?php

namespace AppModules\Invoice\src\Exceptions;

use Exception;

class InvoiceNotFoundException extends Exception
{
    public function __construct(int $invoiceId)
    {
        parent::__construct("Invoice with ID {$invoiceId} not found.");
    }
}
```

### Using Laravel Exception Classes

You can extend Laravel's built-in exceptions for better integration:

```php
<?php

namespace AppModules\Invoice\src\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class InvoiceNotFoundException extends ModelNotFoundException
{
    public function __construct(int $invoiceId)
    {
        parent::__construct("Invoice with ID {$invoiceId} not found.");
    }
}
```

### Domain-Specific Exception

```php
<?php

namespace AppModules\Invoice\src\Exceptions;

use Exception;

class InvoiceAlreadyFinalizedException extends Exception
{
    public function __construct(int $invoiceId)
    {
        parent::__construct("Invoice {$invoiceId} is already finalized and cannot be modified.");
    }
}
```

## Using Exceptions

### Within the Module

```php
<?php

namespace AppModules\Invoice\src\Actions;

use AppModules\Invoice\src\Exceptions\InvoiceNotFoundException;
use AppModules\Invoice\src\Models\Invoice;

class GetInvoiceAction
{
    public function handle(int $invoiceId): Invoice
    {
        $invoice = Invoice::find($invoiceId);
        
        if (!$invoice) {
            throw new InvoiceNotFoundException($invoiceId);
        }
        
        return $invoice;
    }
}
```

### From Other Modules

Exceptions are part of the public API, so other modules can catch them:

```php
<?php

namespace AppModules\Payment\src\Actions;

use AppModules\Invoice\src\Exceptions\InvoiceNotFoundException;
use AppModules\Invoice\src\Exceptions\InvoiceAlreadyFinalizedException;

class ProcessPaymentAction
{
    public function handle(int $invoiceId): void
    {
        try {
            // Use Invoice module's public API
            $invoice = $this->invoiceRepository->find($invoiceId);
            // Process payment...
        } catch (InvoiceNotFoundException $e) {
            // Handle not found
            return redirect()->back()->withErrors(['invoice' => 'Invoice not found']);
        } catch (InvoiceAlreadyFinalizedException $e) {
            // Handle already finalized
            return redirect()->back()->withErrors(['invoice' => 'Invoice is already finalized']);
        }
    }
}
```

## Architecture Testing

Exceptions are part of the public API, so they should be ignored in architecture tests:

```php
test('invoice module boundaries are enforced')
    ->expect('AppModules\Invoice')
    ->toOnlyUse([
        'AppModules\Invoice',
        'Illuminate',
    ])
    ->ignoring('AppModules\Invoice\src\Contracts')
    ->ignoring('AppModules\Invoice\src\DataTransferObjects')
    ->ignoring('AppModules\Invoice\src\Events')
    ->ignoring('AppModules\Invoice\src\Enums')
    ->ignoring('AppModules\Invoice\src\Exceptions'); // ← Exceptions are public API
```

## Best Practices

1. **Be Specific**: Create specific exceptions for different error scenarios
   - ✅ `InvoiceNotFoundException`
   - ✅ `InvoiceAlreadyFinalizedException`
   - ❌ Generic `InvoiceException`

2. **Provide Context**: Include relevant information in the exception message
   ```php
   throw new InvoiceNotFoundException($invoiceId);
   ```

3. **Extend Appropriate Base Classes**: Use Laravel's exception classes when appropriate
   - `ModelNotFoundException` for missing models
   - `ValidationException` for validation errors
   - `AuthorizationException` for permission issues

4. **Document Expected Exceptions**: In PHPDoc, document which exceptions methods can throw
   ```php
   /**
    * @throws InvoiceNotFoundException
    * @throws InvoiceAlreadyFinalizedException
    */
   public function finalizeInvoice(int $invoiceId): Invoice
   ```

## Next Steps

- [Module Structure](./02-module-structure.md) - Complete module structure
- [Cross-Module Communication](./03-cross-module-communication.md) - How modules communicate
- [Enforcing Boundaries](./04-enforcing-boundaries.md) - Architecture testing
