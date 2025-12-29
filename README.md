# Billing Application - Modular Monolith Architecture

Complete Laravel billing application using a **Modular Monolith** architecture inspired by Artisan Airlines (Laracon India 2025).

## 🏗️ Architecture

### Modular Monolith Principle

A modular monolith is a system where all functionality resides in a single codebase, but with **strictly enforced boundaries** between different business domains.

**Advantages:**
- ✅ No network complexity (unlike microservices)
- ✅ ACID transactions maintained
- ✅ Simplified deployment
- ✅ Optimal performance
- ✅ Progressive migration to microservices possible if needed

### Module Structure

Each module follows the **Laravel standard** structure organized by business domain:

```
app-modules/
├── ModuleName/
│   ├── src/                  # Equivalent to app/ - All code
│   │   ├── Http/             # Controllers, Requests
│   │   ├── Models/           # Eloquent Models
│   │   ├── Repositories/     # Data access abstraction
│   │   ├── Services/         # Orchestration services
│   │   ├── Actions/          # Single responsibility actions
│   │   ├── DataTransferObjects/  # DTOs
│   │   ├── Events/           # Events
│   │   ├── Enums/            # PHP Enums
│   │   ├── Exceptions/       # Custom exceptions
│   │   ├── Contracts/        # Public APIs (interfaces)
│   │   ├── Jobs/             # Background tasks
│   │   ├── Listeners/        # Event listeners
│   │   └── Providers/        # Service Provider
│   ├── routes/               # Module routes
│   │   └── web.php
│   ├── database/             # Migrations, Factories, Seeders
│   └── tests/                # Unit and functional tests
```

**Views and Components:**
- Views: `resources/views/modules/{module}/` (loaded with namespace)
- Components: `resources/views/components/{module}/` (anonymous components)

## 📦 Available Modules

### Business Modules

- **User** - User and team management
- **Auth** - Authentication and sessions
- **Dashboard** - Dashboard and statistics
- **Client** - Client management
- **Product** - Product/service catalog
- **Invoice** - Billing core (most complex module)
- **Quote** - Quotes (similar logic to invoices)
- **Payment** - Payment management
- **Document** - Document generation (PDF, Excel)
- **Reporting** - Reports and analytics
- **Settings** - Application configuration

### Shared Module

- **Shared** - Code shared between modules (ValueObjects, Blade Components)

## 🛠️ Custom Artisan Commands

### Create a new module

```bash
php artisan make:module ModuleName
```

Creates a complete module with all necessary folder structure.

### Create an Action in a module

```bash
php artisan make:module-action Invoice CreateInvoiceAction
```

### Create a Service in a module

```bash
php artisan make:module-service Invoice InvoiceService
```

### Create a Repository in a module

```bash
php artisan make:module-repository Invoice InvoiceRepository
```

## 📝 Best Practices

### 1. Actions (Single Responsibility)

Each Action must:
- **Do one thing** (SOLID principle)
- **Be easily testable** unitarily
- **Use dependency injection**
- **Be able to execute in the queue** if necessary

**Example:**
```php
class CreateInvoiceAction
{
    public function __construct(
        private InvoiceRepository $repository,
        private GenerateInvoiceNumberAction $generateNumber
    ) {}

    public function handle(InvoiceData $data): Invoice
    {
        // Business logic here
    }
}
```

### 2. Services for Orchestration

Services orchestrate multiple Actions to implement complex use cases:

```php
class InvoiceService
{
    public function createCompleteInvoice(
        InvoiceData $invoiceData, 
        array $items
    ): Invoice {
        return DB::transaction(function () use ($invoiceData, $items) {
            $invoice = $this->createInvoice->handle($invoiceData);
            // ... orchestration
            return $invoice->fresh();
        });
    }
}
```

### 3. Thin Controllers

Controllers should be thin (< 15 lines) and just delegate to Services:

```php
public function store(StoreInvoiceRequest $request)
{
    $invoice = $this->invoiceService->createCompleteInvoice(
        InvoiceData::fromRequest($request),
        $request->input('items')
    );
    
    return redirect()
        ->route('invoice::show', $invoice)
        ->with('success', 'Invoice created successfully');
}
```

### 4. Inter-Module Communication

Modules communicate via **Events** to avoid direct dependencies:

```php
// Invoice Module
event(new InvoiceCreated($invoice));

// Payment Module (Listener)
class SendPaymentNotification
{
    public function handle(InvoiceCreated $event)
    {
        // React to invoice creation
    }
}
```

### 5. Blade Components with Namespace

Use namespaced components to avoid conflicts:

```blade
<x-invoice::invoice-status :status="$invoice->status" />
<x-shared::button variant="primary">Create</x-shared::button>
```

## 🧪 Tests

Tests are organized by module:

```
app-modules/Invoice/tests/
├── Unit/
│   ├── CalculateInvoiceTotalsActionTest.php
│   └── InvoiceStateMachineTest.php
└── Feature/
    ├── CreateInvoiceTest.php
    └── FinalizeInvoiceTest.php
```

## 🎨 Frontend

- **Blade** for templates
- **Alpine.js** for light interactivity
- **Tailwind CSS** for styling
- **Chart.js** for charts (dashboard)

## 📚 Additional Documentation

- [Module creation guide](docs/creating-a-module.md)
- [Architecture Decision Records](docs/adr/)
- [Naming conventions](docs/naming-conventions.md)

## 🚀 Installation

### Prerequisites

- PHP 8.5.1 or higher
- Composer
- Node.js and npm

### Installation Steps

1. **Clone the project**
   ```bash
   git clone <repository-url>
   cd big-project
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   
   For SQLite (default):
   ```bash
   touch database/database.sqlite
   chmod 664 database/database.sqlite
   ```
   
   Or configure your database in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Compile assets**
   
   For development (with hot-reload):
   ```bash
   npm run dev
   ```
   
   For production:
   ```bash
   npm run build
   ```

7. **Start the server**
   
   Simple mode:
   ```bash
   php artisan serve
   ```
   
   Full development mode (server + queue + logs + Vite):
   ```bash
   composer run dev
   ```

8. **Access the application**
   
   Open your browser and go to: **http://localhost:8000**

## 🎯 Usage

### Basic workflow

1. **Create Clients** → Menu "Clients" → "New Client"
2. **Create Products** → Menu "Products" → "New Product"
3. **Create Invoices** → Menu "Invoices" → "New Invoice"

### Main URLs

- **Dashboard**: `/dashboard`
- **Clients**: `/clients`
- **Products**: `/products`
- **Invoices**: `/invoices`
- **Notifications**: `/notifications`

## 🔧 Useful Commands

### View all routes
```bash
php artisan route:list
```

### Format code
```bash
vendor/bin/pint
```

### Run tests
```bash
php artisan test
```

### Reset database (⚠️ deletes all data)
```bash
php artisan migrate:fresh
```

### Clear caches
```bash
php artisan optimize:clear
composer dump-autoload
```

## 🐛 Troubleshooting

### "Class not found" error
```bash
composer dump-autoload
php artisan optimize:clear
```

### Assets not loading
```bash
npm run build
# or
npm run dev
```

### SQLite database locked
```bash
chmod 664 database/database.sqlite
```

### Routes not working
```bash
php artisan optimize:clear
php artisan route:clear
composer dump-autoload
```

## 📄 License

MIT
