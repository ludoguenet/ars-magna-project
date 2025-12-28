# Episode 2: Module Structure Deep Dive - The Complete Client Flow (3-4 min)

## Introduction

Welcome back! In Episode 1, we saw the high-level setup. Today, let's **walk through a real module** and understand the complete flow from HTTP request to database. We'll trace every step of creating a client user and explain what happens at each layer.

## The Client Module: A Real Example

Let's explore the `Client` module structure. Each module is like a **mini Laravel application** with everything it needs.

### Directory Structure (Visual Walkthrough)

```
app-modules/client/
├── src/                          # The "app" folder
│   ├── Http/
│   │   ├── Controllers/         # Thin controllers (< 15 lines)
│   │   └── Requests/            # Form validation
│   ├── Models/                  # Eloquent models
│   ├── Repositories/            # Data access layer
│   ├── Services/                # Business logic orchestration
│   ├── Contracts/               # Public APIs (interfaces)
│   └── DataTransferObjects/     # DTOs for cross-module communication
├── database/
│   └── migrations/              # Module-specific migrations
├── routes/
│   └── web.php                  # Module routes
├── resources/
│   └── views/                   # Blade templates
└── tests/                       # Module tests
```

## The Complete Flow: Creating a Client User

Let's trace what happens when a user submits the "Create Client" form. We'll see how data flows through each layer and understand every "action" that occurs.

### Step 1: HTTP Request → Controller

**Route:** `POST /clients`

```php
// routes/web.php
Route::post('/clients', [ClientController::class, 'store'])
    ->name('client::store');
```

**Controller Action:**
```php
class ClientController
{
    public function __construct(
        private ClientService $clientService,
        private ClientRepositoryContract $repository
    ) {}

    public function store(StoreClientRequest $request): RedirectResponse
    {
        // Action 1: Convert validated request to DTO
        $clientData = ClientDTO::fromArray($request->validated());
        
        // Action 2: Delegate to service
        $client = $this->clientService->create($clientData);

        return redirect()
            ->route('client::show', $client)
            ->with('success', 'Client created successfully');
    }
}
```

**What happens here:**
- **Action 1:** The `StoreClientRequest` validates the incoming data (name, email, phone, address fields, etc.)
- **Action 2:** Validated data is converted to a `ClientDTO` (Data Transfer Object) - this provides type safety and decouples the controller from the request structure
- **Action 3:** The controller delegates to `ClientService` - controllers stay thin and focused on HTTP concerns

### Step 2: Service Layer - Business Logic Orchestration

The service is where the **real business logic** happens. Let's see what actions occur:

```php
class ClientService
{
    public function __construct(
        private ClientRepositoryContract $repository
    ) {}

    public function create(ClientDTO $data): Client
    {
        // Action 1: Create the User
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email ?? fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
        ]);

        // Action 2: Create the Client with user_id
        $clientData = $data->toArray();
        $clientData['user_id'] = $user->id;
        $client = $this->repository->createFromArray($clientData);

        // Action 3: Create address if provided
        $addressData = $data->getAddressData();
        if ($addressData !== null) {
            $client->address()->create($addressData);
        }

        return $client->fresh(['user', 'address']);
    }
}
```

**Breaking down each action:**

**Action 1: Create User**
- The client's name and email are stored in Laravel's `users` table
- This creates a user account that can authenticate
- If no email is provided, a fake email is generated

**Action 2: Create Client Record**
- The client-specific data (phone, company, VAT number, notes) is stored in the `clients` table
- The `user_id` links the client to the user account
- Uses the repository to abstract data access

**Action 3: Create Address (if provided)**
- Address data (street, city, postal code, country) is stored in a separate `addresses` table
- Uses Eloquent relationship to associate address with client
- Only creates if address data was provided

### Step 3: Repository Layer - Data Access

The repository abstracts database operations:

```php
class ClientRepository implements ClientRepositoryContract
{
    public function createFromArray(array $data): Client
    {
        // Action: Persist to database
        return Client::create($data);
    }
}
```

**What happens:**
- The repository receives an array of data
- Uses Eloquent's `create()` method to insert into the database
- Returns the created `Client` model instance

### Step 4: DTOs - Type-Safe Data Transfer

DTOs (Data Transfer Objects) ensure type safety throughout the flow:

```php
final readonly class ClientDTO
{
    public function __construct(
        public ?int $id = null,
        public string $name = '',
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $company = null,
        public ?string $vatNumber = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $postalCode = null,
        public ?string $country = null,
        public ?string $notes = null,
    ) {}

    // Convert from validated request array
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            // ... other fields
        );
    }

    // Convert to array for Eloquent (client-specific fields only)
    public function toArray(): array
    {
        return array_filter([
            'phone' => $this->phone,
            'company' => $this->company,
            'vat_number' => $this->vatNumber,
            'notes' => $this->notes,
        ], fn ($value) => $value !== null);
    }

    // Extract address data
    public function getAddressData(): ?array
    {
        if (! $this->address && ! $this->city && ! $this->postalCode && ! $this->country) {
            return null;
        }

        return array_filter([
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postalCode,
            'country' => $this->country,
        ], fn ($value) => $value !== null);
    }
}
```

**Why DTOs?**
- ✅ **Type safety** - IDE autocomplete, static analysis catches errors
- ✅ **Clear contracts** - What data is expected is explicit
- ✅ **Prevents coupling** - Controller doesn't depend on request structure
- ✅ **Easy to test** - Can create DTOs directly in tests

## The Update Flow

Updating a client follows a similar pattern but with additional actions:

```php
public function update(Client $client, ClientDTO $data): Client
{
    // Load relationships
    $client->load('user', 'address');

    // Action 1: Update User's name and email if changed
    if ($client->user) {
        $userUpdates = [];
        if ($data->name !== $client->user->name) {
            $userUpdates['name'] = $data->name;
        }
        if ($data->email !== null && $data->email !== $client->user->email) {
            $userUpdates['email'] = $data->email;
        }
        if (! empty($userUpdates)) {
            $client->user->update($userUpdates);
        }
    }

    // Action 2: Update client-specific fields
    $this->repository->update($client, $data);

    // Action 3: Update or create address
    $addressData = $data->getAddressData();
    if ($addressData !== null) {
        if ($client->address) {
            $client->address->update($addressData);
        } else {
            $client->address()->create($addressData);
        }
    } elseif ($client->address) {
        // Action 4: Delete address if removed
        $client->address->delete();
    }

    return $client->fresh(['user', 'address']);
}
```

**Update Actions:**
1. **Update User** - Syncs name/email changes to the users table
2. **Update Client** - Updates client-specific fields via repository
3. **Update/Create Address** - Handles address changes or creation
4. **Delete Address** - Removes address if all address fields are cleared

## Key Principles

### 1. Single Responsibility Layers

**Controllers** → Thin, just delegate to services
- Handle HTTP concerns (request/response)
- Convert requests to DTOs
- Return redirects/views

**Services** → Orchestrate business logic
- Coordinate multiple actions
- Handle business rules
- Manage transactions if needed

**Repositories** → Abstract data access
- Accept DTOs, return models or DTOs
- Hide database implementation details
- Provide a clean interface for data operations

### 2. Contracts = Public API

The `Contracts/` folder defines what other modules can use. This is the **only** way modules communicate.

```php
// Other modules depend on this interface, not the implementation
interface ClientRepositoryContract
{
    public function find(int $id): ?ClientDTO;
    public function search(string $query): array<ClientDTO>;
}
```

### 3. DTOs for Data Transfer

DTOs are used for **both input and output**. They provide type safety and prevent tight coupling between layers.

## What Makes This Different?

**Traditional Laravel:**
```
app/
├── Models/          # All models together
├── Http/Controllers/ # All controllers together
└── Services/        # All services together
```

**Modular Monolith:**
```
app-modules/
├── client/          # Everything client-related
├── invoice/         # Everything invoice-related
└── product/         # Everything product-related
```

## The Service Provider

Each module has a `ServiceProvider` that:
- Registers routes
- Binds contracts to implementations
- Loads migrations
- Registers views

```php
class ClientServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind contract to implementation
        $this->app->bind(
            ClientRepositoryContract::class,
            ClientRepository::class
        );
    }

    public function boot(): void
    {
        // Load routes, migrations, views
    }
}
```

## Summary

**Module Structure = Mini Laravel App**
- Domain-centric organization
- Single responsibility layers
- Contracts define public APIs
- DTOs for safe data transfer

**The Complete Flow:**
1. **Request** → Controller receives HTTP request
2. **Validation** → Form Request validates data
3. **DTO Conversion** → Validated data becomes type-safe DTO
4. **Service Orchestration** → Service coordinates multiple actions (create user, create client, create address)
5. **Repository** → Data access layer persists to database
6. **Response** → Controller returns redirect/view

**Key Takeaway:** Everything a module needs lives in its folder. Each layer has a clear responsibility, and data flows through DTOs for type safety.

**Next Episode:** We'll explore cross-module communication with more complex actions involving **Events**. You'll see how the Invoice module uses Actions (like `CreateInvoiceAction`, `FinalizeInvoiceAction`) that trigger events, and how other modules (Notification, Payment) listen to those events to perform their own actions. This is where the real power of modular architecture shines!