# Episode 3: Cross-Module Communication with Events (4-5 min)

## Introduction

Welcome back! In Episode 2, we saw how a single module handles requests from HTTP to database. Today, we'll explore **cross-module communication** - the real power of modular architecture. We'll see how modules communicate through **Events** without creating tight coupling.

## The Problem: Modules Need to Talk

When an invoice is created, multiple things need to happen:
- **Payment module** needs to create a payment record
- **Notification module** needs to notify the user
- **Invoice module** needs to generate PDFs and send emails

**The challenge:** How do modules react to each other's actions **without** creating dependencies?

## The Solution: Events & Listeners

Laravel's event system is perfect for this. Modules can **dispatch events** when important actions occur, and other modules can **listen** to those events without knowing about each other.

### The Flow: Invoice Creation

Let's trace what happens when a user creates an invoice. We'll see how one action triggers multiple reactions across different modules.

#### Step 1: Controller → Action

```php
// InvoiceController
public function store(StoreInvoiceRequest $request): RedirectResponse
{
    $invoice = $this->invoiceService->createCompleteInvoice(
        InvoiceDTO::fromRequest($request),
        $request->input('items', [])
    );

    return redirect()
        ->route('invoice::show', $invoice)
        ->with('success', 'Invoice created successfully');
}
```

The controller delegates to the service, which eventually calls `CreateInvoiceAction`.

#### Step 2: Action Creates Invoice & Dispatches Event

```php
// CreateInvoiceAction
class CreateInvoiceAction
{
    public function handle(InvoiceDTO $data): Invoice
    {
        // 1. Generate invoice number
        $invoiceNumber = $this->generateNumber->handle($data->clientId);

        // 2. Create invoice in database
        $invoice = $this->repository->create([
            'invoice_number' => $invoiceNumber,
            'client_id' => $data->clientId,
            'status' => InvoiceStatus::DRAFT,
            // ... other fields
        ]);

        // 3. Convert to DTO and dispatch event
        $invoiceDTO = InvoiceDTO::fromModel($invoice->load(['client', 'items']));
        event(new InvoiceCreated($invoiceDTO));

        return $invoice;
    }
}
```

**Key point:** The action dispatches `InvoiceCreated` event **after** the invoice is created. The event contains an `InvoiceDTO` - a type-safe data structure that other modules can use.

#### Step 3: Event Definition

```php
// InvoiceCreated event
class InvoiceCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public InvoiceDTO $invoice
    ) {}
}
```

Events are simple classes that carry data. They use DTOs to ensure type safety and prevent coupling to models.

## Listeners React to Events

Now let's see how other modules react to this event.

### Payment Module Listener

When an invoice is created, the Payment module needs to:
1. Create a payment record
2. Schedule a payment reminder

```php
// Payment module: HandleInvoiceCreated listener
class HandleInvoiceCreated
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function handle(InvoiceCreated $event): void
    {
        $invoice = $event->invoice;

        // 1. Create payment record
        $payment = $this->paymentService->createPaymentForInvoice($invoice);

        // 2. Schedule reminder 7 days before due date
        if ($invoice->dueAt) {
            $reminderDate = $invoice->dueAt->copy()->subDays(7);

            if ($reminderDate->isFuture()) {
                SendPaymentReminderJob::dispatch($invoice->id)
                    ->delay($reminderDate);
            }
        }
    }
}
```

**What's happening:**
- The listener receives the `InvoiceCreated` event
- It extracts the `InvoiceDTO` from the event
- It uses its own `PaymentService` to create a payment record
- It schedules a background job for the reminder

**Key point:** The Payment module doesn't know about Invoice models or repositories. It only knows about the `InvoiceDTO` from the event.

### Notification Module Listener

The Notification module creates a notification for the user:

```php
// Notification module: HandleInvoiceCreated listener
class HandleInvoiceCreated
{
    public function __construct(
        private CreateNotificationAction $createNotificationAction
    ) {}

    public function handle(InvoiceCreated $event): void
    {
        $invoice = $event->invoice;

        if (! auth()->check()) {
            return;
        }

        $this->createNotificationAction->handle(
            new NotificationDTO(
                userId: auth()->id(),
                type: NotificationType::INVOICE_CREATED,
                title: 'New Invoice Created',
                message: "Invoice #{$invoice->invoiceNumber} has been created. Total: ".number_format($invoice->total, 2).'.',
                data: [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoiceNumber,
                    'total' => $invoice->total,
                ]
            )
        );
    }
}
```

**What's happening:**
- The listener creates a notification using its own action
- It uses data from the `InvoiceDTO` but doesn't depend on Invoice models
- The notification is stored in the Notification module's own database

## Registering Listeners

There are two ways to register listeners in Laravel 12:

### Method 1: Auto-Discovery (Recommended)

Laravel 12 can auto-discover listeners. In `bootstrap/app.php`:

```php
->withEvents(discover: [
    __DIR__.'/../app-modules/*/src/Listeners',
])
```

Laravel automatically finds all listener classes in `Listeners` directories and registers them based on type hints.

**How it works:**
- Laravel scans `app-modules/*/src/Listeners` directories
- It finds classes with `handle()` methods
- It reads the type hint of the `handle()` method parameter
- It automatically registers: `Event::listen(InvoiceCreated::class, HandleInvoiceCreated::class)`

**Benefits:**
- ✅ No manual registration needed
- ✅ Listeners are automatically discovered
- ✅ Works across all modules

### Method 2: Manual Registration

For explicit control, register listeners in the ServiceProvider:

```php
// NotificationServiceProvider
public function boot(): void
{
    Event::listen(InvoiceCreated::class, HandleInvoiceCreated::class);
    Event::listen(InvoicePaid::class, HandleInvoicePaid::class);
}
```

**When to use:**
- When you need conditional registration
- When you want explicit documentation of what listens to what
- When auto-discovery doesn't work for your use case

## Complete Flow: Invoice Lifecycle

Let's trace the complete lifecycle of an invoice through events:

### 1. Invoice Created

**Action:** `CreateInvoiceAction` creates invoice
**Event:** `InvoiceCreated` dispatched
**Listeners:**
- `Payment\HandleInvoiceCreated` → Creates payment record, schedules reminder
- `Notification\HandleInvoiceCreated` → Creates notification

### 2. Invoice Finalized

**Action:** `FinalizeInvoiceAction` changes status from DRAFT to SENT
**Event:** `InvoiceFinalized` dispatched
**Listener:**
- `Invoice\HandleInvoiceFinalized` → Generates PDF, sends email

```php
// FinalizeInvoiceAction
public function handle(Invoice $invoice): Invoice
{
    if (! $invoice->status->canBeFinalized()) {
        throw new \DomainException('Invoice cannot be finalized.');
    }

    $invoice->update(['status' => InvoiceStatus::SENT]);

    $invoiceDTO = InvoiceDTO::fromModel($invoice->fresh()->load(['client', 'items']));
    event(new InvoiceFinalized($invoiceDTO));

    return $invoice;
}
```

```php
// HandleInvoiceFinalized listener (in Invoice module itself)
public function handle(InvoiceFinalized $event): void
{
    $invoice = $event->invoice;

    // Generate PDF in background
    GenerateInvoicePDFJob::dispatch($invoice->id);

    // Send email in background
    SendInvoiceEmailJob::dispatch($invoice->id);
}
```

**Note:** The Invoice module can listen to its own events! This keeps related logic together while still using the event system.

### 3. Invoice Paid

**Action:** `MarkInvoiceAsPaidAction` changes status to PAID
**Event:** `InvoicePaid` dispatched
**Listeners:**
- `Payment\HandleInvoicePaid` → Marks payment as completed
- `Notification\HandleInvoicePaid` → Creates "payment received" notification

```php
// MarkInvoiceAsPaidAction
public function handle(Invoice $invoice): Invoice
{
    if (! $invoice->status->canBePaid()) {
        throw new \DomainException('Invoice cannot be marked as paid.');
    }

    $invoice->update(['status' => InvoiceStatus::PAID]);

    $invoiceDTO = InvoiceDTO::fromModel($invoice->fresh()->load(['client', 'items']));
    event(new InvoicePaid($invoiceDTO));

    return $invoice;
}
```

```php
// Payment module: HandleInvoicePaid
public function handle(InvoicePaid $event): void
{
    $invoice = $event->invoice;

    // Find the payment record
    $payments = $this->paymentRepository->getByInvoiceId($invoice->id);
    $pendingPayment = $payments->first(fn ($payment) => $payment->isPending());

    if ($pendingPayment) {
        // Mark payment as completed
        $this->paymentService->markAsCompleted($pendingPayment);
    }
}
```

## Why Events Over Direct Calls?

### ❌ Without Events (Tight Coupling)

```php
// Invoice module directly calls Payment module
class CreateInvoiceAction
{
    public function handle(InvoiceDTO $data): Invoice
    {
        $invoice = $this->repository->create([...]);

        // ❌ BAD: Direct dependency on Payment module
        $paymentService = app(PaymentService::class);
        $paymentService->createPaymentForInvoice($invoice);

        return $invoice;
    }
}
```

**Problems:**
- Invoice module depends on Payment module
- Can't create invoices without Payment module
- Hard to test (need to mock PaymentService)
- Violates module boundaries

### ✅ With Events (Loose Coupling)

```php
// Invoice module just dispatches event
class CreateInvoiceAction
{
    public function handle(InvoiceDTO $data): Invoice
    {
        $invoice = $this->repository->create([...]);
        $invoiceDTO = InvoiceDTO::fromModel($invoice);
        
        // ✅ GOOD: Just dispatch event, don't know who listens
        event(new InvoiceCreated($invoiceDTO));

        return $invoice;
    }
}
```

**Benefits:**
- ✅ Invoice module doesn't know about Payment or Notification
- ✅ Can create invoices even if no listeners exist
- ✅ Easy to test (just assert event was dispatched)
- ✅ Other modules can be added/removed without changing Invoice code
- ✅ Modules stay independent

## DTOs in Events

Notice that events carry **DTOs**, not models:

```php
// ✅ GOOD: Event carries DTO
event(new InvoiceCreated(InvoiceDTO::fromModel($invoice)));

// ❌ BAD: Event carries model
event(new InvoiceCreated($invoice));
```

**Why DTOs?**
- **Type safety** - Clear contract of what data is available
- **No coupling** - Listeners don't depend on Invoice models
- **Serialization** - DTOs serialize cleanly for queues
- **Versioning** - Can evolve DTOs without breaking listeners

## Architecture Benefits

### 1. Independent Development

Teams can work on modules independently:
- Invoice team creates events
- Payment team creates listeners
- No merge conflicts or coordination needed

### 2. Easy Testing

```php
// Test that event is dispatched
Event::fake();

$action->handle($invoiceData);

Event::assertDispatched(InvoiceCreated::class);
```

### 3. Flexible Composition

Add new functionality without changing existing code:

```php
// New Reporting module listens to InvoiceCreated
class TrackInvoiceCreated
{
    public function handle(InvoiceCreated $event): void
    {
        // Track invoice creation for analytics
    }
}
```

No changes needed to Invoice, Payment, or Notification modules!

### 4. Clear Boundaries

Events define the **public API** between modules:
- Invoice module publishes: `InvoiceCreated`, `InvoiceFinalized`, `InvoicePaid`
- Other modules subscribe to these events
- No direct model access across boundaries

## Summary

**Events Enable Cross-Module Communication:**
- ✅ Modules dispatch events when important actions occur
- ✅ Other modules listen to events and react
- ✅ No direct dependencies between modules
- ✅ Easy to add/remove functionality

**The Complete Pattern:**
1. **Action** performs business logic
2. **Action** dispatches **Event** with **DTO**
3. **Listeners** (in other modules) react to event
4. **Listeners** use their own services/actions
5. Modules stay independent

**Key Takeaway:** Events are the **glue** that connects modules without creating coupling. They enable reactive, event-driven architecture while maintaining strict boundaries.

**Next Episode:** We'll explore **Actions** - single-purpose classes that encapsulate business logic. You'll see how Actions differ from Services, when to use each, and how they make code more testable and maintainable.
