# Module Structure

Each module is like a **mini Laravel application** that contains everything it needs.

## Directory Structure

```
app-modules/
├── {module}/
│   ├── src/                    # Equivalent to app/ folder
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   └── Requests/
│   │   ├── Models/            # Eloquent models
│   │   ├── Repositories/      # Data access abstraction
│   │   ├── Services/          # Business logic orchestration
│   │   ├── Actions/           # Single-responsibility actions
│   │   ├── DataTransferObjects/  # Data Transfer Objects
│   │   ├── Events/            # Domain events
│   │   ├── Enums/             # PHP Enums
│   │   ├── Exceptions/        # Custom exceptions
│   │   ├── Contracts/          # Public APIs (interfaces)
│   │   ├── Jobs/              # Background jobs
│   │   └── Listeners/         # Event listeners
│   ├── database/
│   │   └── migrations/
│   ├── routes/
│   │   └── web.php            # Module routes
│   ├── tests/
│   └── src/Providers/
│       └── {Module}ServiceProvider.php
```

## Views and Components

- **Views**: `resources/views/modules/{module}/` (loaded with namespace)
- **Components**: `resources/views/components/{module}/` (anonymous components)

## Key Principles

### 1. Domain-Centric Organization

Code is organized by **business domain** (Invoice, Client, Product) rather than by technical layer (Models, Controllers).

### 2. Single Responsibility

- **Actions**: Do one thing (e.g., `CreateInvoiceAction`)
- **Services**: Orchestrate multiple actions (e.g., `InvoiceService`)
- **Controllers**: Just delegate to services (thin controllers, < 15 lines)
- **Repositories**: Abstract data access

### 3. Laravel Conventions First

We use standard Laravel features:
- ✅ Eloquent ORM
- ✅ Blade templates
- ✅ Service Container (dependency injection)
- ✅ Events & Listeners
- ✅ Jobs & Queues

## Example: Simple Module

```php
// src/Http/Controllers/ProductController.php
class ProductController
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create($request->validated());
        return redirect()->route('product::show', $product);
    }
}

// src/Services/ProductService.php
class ProductService
{
    public function create(array $data): Product
    {
        return Product::create($data);
    }
}

// src/Models/Product.php
class Product extends Model
{
    protected $fillable = ['name', 'price'];
}
```

## Next Steps

- [Cross-Module Communication](./03-cross-module-communication.md) - How modules talk to each other
- [Creating a Module](./creating-a-module.md) - Step-by-step guide
