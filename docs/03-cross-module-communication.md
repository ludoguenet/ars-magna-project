# Cross-Module Communication

Modules communicate through **Contracts** (interfaces) and **Events**, not direct dependencies.

## Contracts (Interfaces)

**Contracts** are PHP interfaces that define the public API between modules. They encapsulate implementation details.

### Example

```php
// Flight module defines the contract
namespace AppModules\Flight\src\Contracts;

interface FlightRepositoryContract
{
    public function find(int $flightId): FlightDTO;
}

// Flight module implements it
class FlightRepository implements FlightRepositoryContract
{
    public function find(int $flightId): FlightDTO
    {
        $flight = Flight::findOrFail($flightId);
        return FlightDTO::fromModel($flight);
    }
}

// Booking module uses the contract
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

### Binding Contracts

Bind the contract to implementation in the Service Provider:

```php
// FlightServiceProvider
public function register(): void
{
    $this->app->bind(
        FlightRepositoryContract::class,
        FlightRepository::class
    );
}
```

## Exceptions

**Exceptions** are part of a module's public API. Other modules can catch and handle them.

### Example

```php
// src/Exceptions/InvoiceNotFoundException.php
namespace AppModules\Invoice\src\Exceptions;

class InvoiceNotFoundException extends Exception
{
    public function __construct(int $invoiceId)
    {
        parent::__construct("Invoice {$invoiceId} not found.");
    }
}
```

Other modules can catch this exception:

```php
try {
    $invoice = $this->invoiceRepository->find($id);
} catch (InvoiceNotFoundException $e) {
    // Handle error
}
```

See [Exceptions](./07-exceptions.md) for detailed guidance.

## DTOs (Data Transfer Objects)

**DTOs** are simple data containers, no behavior. Use them instead of returning Eloquent models.

### Why DTOs?

- Prevents other modules from manipulating the database directly
- Hides implementation details
- Makes testing easier (can mock the contract)
- Allows teams to work in parallel

### Example

```php
// src/DataTransferObjects/FlightDTO.php
final readonly class FlightDTO
{
    public function __construct(
        public int $id,
        public string $number,
        public string $origin,
        public string $destination,
    ) {}
    
    public static function fromModel(Flight $flight): self
    {
        return new self(
            id: $flight->id,
            number: $flight->number,
            origin: $flight->origin,
            destination: $flight->destination,
        );
    }
}
```

## Events

**Events** enable modules to react to changes without direct dependencies.

### Example

```php
// Invoice module fires an event
event(new InvoicePaid($invoice));

// Payment module listens (registered in EventServiceProvider)
class SendPaymentConfirmation
{
    public function handle(InvoicePaid $event)
    {
        // React to invoice being paid
    }
}
```

## Testing Cross-Module Communication

Mock contracts when testing:

```php
test('booking payment processes correctly', function () {
    Event::fake();
    
    $booking = Booking::factory()->create();
    
    // Mock contracts from other modules
    $this->mock(SeatRepositoryContract::class, function ($mock) {
        $mock->shouldReceive('markAsBooked')->once()->andReturn(true);
    });
    
    $this->mock(PaymentContract::class, function ($mock) {
        $mock->shouldReceive('process')->once()->andReturn('payment_123');
    });
    
    $response = $this->post(route('booking::payment.store', $booking));
    
    $response->assertRedirect(route('booking::success'));
    Event::assertDispatched(BookingConfirmed::class);
});
```

## Next Steps

- [Enforcing Boundaries](./04-enforcing-boundaries.md) - Architecture testing
- [Creating a Module](./creating-a-module.md) - Step-by-step guide
