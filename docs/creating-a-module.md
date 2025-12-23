# Creating a Module

Quick guide to create a new module following Ryuta's Modular Monolith approach.

## Quick Start

```bash
php artisan make:module Product
```

This creates the complete module structure automatically.

## Basic Structure

```
app-modules/product/
├── src/
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Repositories/
│   └── Services/
├── database/migrations/
├── routes.php
└── src/Providers/ProductServiceProvider.php
```

## Step 1: Create the Module

Utilisez la commande Artisan pour créer la structure de base :

```bash
php artisan make:module Product
```

## Step 2: Create Models

Create Eloquent models in `src/Models/`:

```php
<?php

namespace AppModules\Product\src\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        // ...
    ];
}
```

## Step 3: Create Migrations

Create migrations in `database/migrations/`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // ...
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

## Step 4: Create Actions

Create actions with:

```bash
php artisan make:module-action Product CreateProductAction
```

Then implement the logic:

```php
<?php

namespace AppModules\Product\src\Actions;

use AppModules\Product\src\Models\Product;
use AppModules\Product\src\Repositories\ProductRepository;

class CreateProductAction
{
    public function __construct(
        private ProductRepository $repository
    ) {}

    public function handle(array $data): Product
    {
        return $this->repository->create($data);
    }
}
```

## Step 5: Create Service (if needed)

To orchestrate multiple actions:

```bash
php artisan make:module-service Product ProductService
```

## Step 6: Create Repository

```bash
php artisan make:module-repository Product ProductRepository
```

## Step 7: Create Controller

Create controller in `src/Http/Controllers/`:

```php
<?php

namespace AppModules\Product\src\Http\Controllers;

use AppModules\Product\src\Services\ProductService;
use Illuminate\Http\Request;

class ProductController
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index()
    {
        // ...
    }
}
```

## Step 8: Create Routes

Create `routes/web.php` in the module:

```php
<?php

use AppModules\Product\Application\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('products')->name('product::')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    // ...
});
```

## Step 9: Create Views

Create Blade views in `resources/views/modules/{module}/`:

Views are loaded with a namespace, so use `module::view-name` in controllers.

```blade
@extends('layouts.app')

@section('content')
    <h1>Products</h1>
@endsection
```

## Step 10: Create Contracts (if module is used by others)

If other modules need to access this module's data, create a **Contract** (interface):

```php
<?php

namespace AppModules\Product\src\Contracts;

use AppModules\Product\src\DataTransferObjects\ProductDTO;

interface ProductRepositoryContract
{
    public function find(int $id): ?ProductDTO;
    public function all(): Collection;
}
```

Puis implémentez-le dans le module :

```php
<?php

namespace AppModules\Product\src\Repositories;

use AppModules\Product\src\Contracts\ProductRepositoryContract;
use AppModules\Product\src\DataTransferObjects\ProductDTO;
use AppModules\Product\src\Models\Product;

class ProductRepository implements ProductRepositoryContract
{
    public function find(int $id): ?ProductDTO
    {
        $product = Product::find($id);
        return $product ? ProductDTO::fromModel($product) : null;
    }
}
```

Bind it in the Service Provider:

```php
public function register(): void
{
    $this->app->bind(
        ProductRepositoryContract::class,
        ProductRepository::class
    );
}
```

## Step 11: Create DTOs (for cross-module communication)

DTOs (Data Transfer Objects) are simple data containers with no behavior:

```php
<?php

namespace AppModules\Product\src\DataTransferObjects;

final readonly class ProductDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public float $price,
    ) {}
    
    public static function fromModel(Product $product): self
    {
        return new self(
            id: $product->id,
            name: $product->name,
            description: $product->description,
            price: (float) $product->price,
        );
    }
}
```

**Why DTOs?**
- Prevents other modules from manipulating Eloquent models directly
- Hides implementation details (DB structure, relationships)
- Makes testing easier (can mock easily)
- Allows teams to work in parallel

## Step 12: Create Exceptions (for error handling)

Custom exceptions are part of a module's public API. Create domain-specific exceptions:

```php
<?php

namespace AppModules\Product\src\Exceptions;

use Exception;

class ProductNotFoundException extends Exception
{
    public function __construct(int $productId)
    {
        parent::__construct("Product with ID {$productId} not found.");
    }
}
```

Use them in your actions:

```php
use AppModules\Product\src\Exceptions\ProductNotFoundException;

class GetProductAction
{
    public function handle(int $productId): Product
    {
        $product = Product::find($productId);
        
        if (!$product) {
            throw new ProductNotFoundException($productId);
        }
        
        return $product;
    }
}
```

See [Exceptions](./07-exceptions.md) for detailed guidance.

## Step 13: Create Enums

PHP Enums live in `src/Enums/`:

```php
<?php

namespace AppModules\Product\src\Enums;

enum ProductStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::ARCHIVED => 'Archived',
        };
    }
}
```

## Step 14: Create Events (for cross-module communication)

Events allow modules to react to changes without direct dependencies:

```php
<?php

namespace AppModules\Product\src\Events;

use AppModules\Product\src\Models\Product;

class ProductCreated
{
    public function __construct(public Product $product) {}
}
```

Then in your service:

```php
public function create(array $data): Product
{
    $product = Product::create($data);
    event(new ProductCreated($product));
    return $product;
}
```

## Step 14: Register Module

Module is automatically discovered by `ModuleServiceProvider`. Ensure:

1. Service Provider exists: `src/Providers/ProductServiceProvider.php`
2. Namespace is correct: `AppModules\Product\src\Providers\ProductServiceProvider`
3. Run: `composer dump-autoload`

## Step 15: Architecture Tests (Enforce Boundaries)

Create an architecture test to ensure module boundaries are respected:

```php
<?php

namespace AppModules\Product\tests;

test('product module boundaries are enforced')
    ->expect('AppModules\Product')
    ->toOnlyUse([
        'AppModules\Product',
        'Illuminate',
        // Contracts, DTOs, Events, Enums, Exceptions are public APIs
    ])
    ->ignoring('AppModules\Product\src\Contracts')
    ->ignoring('AppModules\Product\src\DataTransferObjects')
    ->ignoring('AppModules\Product\src\Events')
    ->ignoring('AppModules\Product\src\Enums')
    ->ignoring('AppModules\Product\src\Exceptions');
```

This test will fail if the module uses classes from other modules directly.

## Complete Example

See the `Invoice` module for a complete example with:
- Models with relationships
- Multiple actions
- Orchestration services
- Contracts for cross-module communication
- DTOs for data transfer
- Events for cross-module reactions
- Background jobs
- Blade views and components
- Unit and feature tests
- Architecture tests to enforce boundaries

## Related Documentation

- [Module Structure](./02-module-structure.md) - How modules are organized
- [Cross-Module Communication](./03-cross-module-communication.md) - Contracts, DTOs, Events
- [Enforcing Boundaries](./04-enforcing-boundaries.md) - Architecture testing
