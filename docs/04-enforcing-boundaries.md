# Enforcing Module Boundaries

Use **Pest Architecture Testing** to ensure modules don't cross boundaries.

## Architecture Testing

Create a test in each module to enforce boundaries:

```php
// app-modules/flight/tests/ArchitectureTest.php
test('flight module boundaries are enforced')
    ->expect('AppModules\Flight')
    ->toOnlyUse([
        'AppModules\Flight',
        'Illuminate',
        // Contracts, DTOs, Events, Enums, Exceptions are public APIs
    ])
    ->ignoring('AppModules\Flight\src\Contracts')
    ->ignoring('AppModules\Flight\src\DataTransferObjects')
    ->ignoring('AppModules\Flight\src\Events')
    ->ignoring('AppModules\Flight\src\Enums')
    ->ignoring('AppModules\Flight\src\Exceptions');
```

## What This Ensures

- ✅ Modules can only use their own code + Laravel + public APIs
- ✅ Direct model access across modules is prevented
- ✅ CI/CD will fail if boundaries are violated

## Public APIs

These are considered public and can be used by other modules:

- **Contracts** (`src/Contracts/`) - Interfaces
- **DTOs** (`src/DataTransferObjects/`) - Data Transfer Objects
- **Events** (`src/Events/`) - Domain events
- **Enums** (`src/Enums/`) - PHP Enumerations
- **Exceptions** (`src/Exceptions/`) - Custom exceptions

## Example: Violation

If you try to use a model from another module directly:

```php
// ❌ This will fail the architecture test
class BookingController
{
    public function create(Request $request)
    {
        $flight = Flight::find($request->flight_id); // Violates boundary!
    }
}
```

The test will fail, preventing deployment.

## Example: Correct Approach

```php
// ✅ This passes the architecture test
class BookingController
{
    public function __construct(
        private FlightRepositoryContract $flightRepository
    ) {}
    
    public function create(Request $request)
    {
        $flight = $this->flightRepository->find($request->flight_id);
    }
}
```

## Next Steps

- [Scaling](./05-scaling.md) - How the architecture scales
- [Creating a Module](./creating-a-module.md) - Step-by-step guide
