# Episode 1: Why Modular Monolith & How We Handle Modules (Without Packages)

## Introduction

Welcome to the first episode! Today we're going to explore why we chose a Modular Monolith architecture for this Laravel application, and most importantly, how we implemented it **without using any third-party modular packages** like nwidart/laravel-modules or internachi/laravel-modules.

## Part 1: Why Modular Monolith?

### The Problem with Traditional Laravel Structure

When you start a Laravel project, you get a clean structure:
- `app/Models/` - all your models
- `app/Http/Controllers/` - all your controllers
- `resources/views/` - all your views

This works great for small to medium applications. But what happens when your application grows?

**Real problems we face:**
1. **Code scattering** - Related code is spread across different directories
   - Client model in `app/Models/Client.php`
   - Client controller in `app/Http/Controllers/ClientController.php`
   - Client views in `resources/views/clients/`
   - Client service logic... where? Maybe in the controller? Maybe in a service class?

2. **Parallel development becomes difficult**
   - Multiple developers working on different features touch the same files
   - Merge conflicts increase
   - Hard to work independently

3. **No clear boundaries**
   - What belongs to the "Client" feature vs "Invoice" feature?
   - Easy to create tight coupling between features
   - Hard to test in isolation

4. **Scaling issues**
   - With 50+ models, finding related code becomes a treasure hunt
   - Onboarding new developers is harder
   - Refactoring becomes risky

### The Solution: Modular Monolith

A **Modular Monolith** keeps everything in one codebase (like a traditional monolith) but enforces **strict boundaries** between business domains.

**Key benefits:**
- ✅ **Simple deployment** - One codebase, one deployment pipeline
- ✅ **ACID transactions** - Can use database transactions across modules
- ✅ **Well-defined boundaries** - Each module is isolated and self-contained
- ✅ **Parallel development** - Teams can work on different modules independently
- ✅ **Easy onboarding** - New developers can focus on one module at a time
- ✅ **Migration path** - Can extract modules to microservices later if needed

**When NOT to use it:**
- Small applications (overengineering)
- Simple CRUD apps
- Start with traditional monolith, migrate when you feel the pain

## Part 2: How We Handle Modules (The Custom Way)

### Overview: No Third-Party Packages

We're NOT using packages like:
- ❌ `nwidart/laravel-modules`
- ❌ `internachi/laravel-modules`
- ❌ Any other modular package

**Why?** Because we want:
- Full control over the structure
- To stay close to Laravel conventions
- No "magic" - everything is explicit and understandable
- Less dependencies to maintain

### Step 1: Directory Structure

We use a simple convention: all modules live in `app-modules/`

```
app-modules/
├── client/
│   ├── src/              # All module code (equivalent to app/)
│   │   ├── Http/
│   │   ├── Models/
│   │   ├── Services/
│   │   └── Providers/
│   ├── routes/
│   ├── database/
│   └── tests/
├── invoice/
├── product/
└── ...
```

Each module is a **mini Laravel application** with its own:
- Controllers
- Models
- Services
- Routes
- Migrations
- Tests

### Step 2: Composer.json Configuration

The key is in `composer.json` - we add a custom PSR-4 autoload namespace:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "AppModules\\": "app-modules/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    }
}
```

This tells Composer: "When you see a class in the `AppModules\` namespace, look for it in the `app-modules/` directory."

**Important:** After modifying `composer.json`, always run:
```bash
composer dump-autoload
```

### Step 3: Custom Autoloader (Case Sensitivity)

Laravel's file system might be case-insensitive (like on macOS/Windows), but PHP namespaces are case-sensitive. We need a custom autoloader to handle this.

In `app/Providers/AppServiceProvider.php`:

```php
public function register(): void
{
    // Register custom autoloader for AppModules to handle case-sensitivity
    spl_autoload_register(function (string $class): void {
        // Only handle AppModules namespace
        if (strpos($class, 'AppModules\\') !== 0) {
            return;
        }

        // Convert namespace to file path
        $relativeClass = substr($class, strlen('AppModules\\'));
        $parts = explode('\\', $relativeClass);

        // First part is the module name - convert to lowercase for directory
        $moduleName = strtolower(array_shift($parts));
        $file = base_path("app-modules/{$moduleName}/".implode('/', $parts).'.php');

        if (file_exists($file)) {
            require_once $file;
        }
    }, true, true);
}
```

This autoloader:
1. Intercepts class loading for `AppModules\` namespace
2. Converts the namespace to a file path
3. Handles case-insensitivity by converting module names to lowercase
4. Loads the file if it exists

### Step 4: Module Discovery & Service Provider Registration

We created a `ModuleServiceProvider` that automatically discovers and registers all modules.

In `app/Providers/ModuleServiceProvider.php`:

```php
public function register(): void
{
    $modulesPath = base_path('app-modules');

    if (! File::isDirectory($modulesPath)) {
        return;
    }

    $modules = File::directories($modulesPath);

    foreach ($modules as $modulePath) {
        $moduleDir = basename($modulePath);
        $moduleName = Str::studly($moduleDir);
        $providerClass = "AppModules\\{$moduleName}\\src\\Providers\\{$moduleName}ServiceProvider";

        if (class_exists($providerClass)) {
            $this->app->register($providerClass);
        }
    }
}
```

**How it works:**
1. Scans the `app-modules/` directory
2. For each subdirectory (module), it constructs the expected ServiceProvider class name
3. If the ServiceProvider exists, it registers it

**Example:**
- Directory: `app-modules/client/`
- Module name: `Client` (converted from `client` using `Str::studly()`)
- Expected provider: `AppModules\Client\src\Providers\ClientServiceProvider`
- If it exists → register it!

### Step 5: Register ModuleServiceProvider

In `bootstrap/providers.php` (Laravel 11+ structure):

```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ModuleServiceProvider::class,  // ← Our custom provider
];
```

This ensures our module discovery runs on every application boot.

### Step 6: Module Service Providers

Each module has its own ServiceProvider that:
- Loads routes
- Loads migrations
- Loads views with namespace
- Registers bindings

Example: `app-modules/client/src/Providers/ClientServiceProvider.php`

```php
class ClientServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Load routes
        if (file_exists($routesPath = __DIR__.'/../../routes/web.php')) {
            $this->loadRoutesFrom($routesPath);
        }

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load views with namespace
        $this->loadViewsFrom(
            resource_path('views/modules/client'),
            'client'
        );
    }
}
```

### Step 7: Event Discovery (Laravel 11+)

In `bootstrap/app.php`, we can also auto-discover event listeners:

```php
->withEvents(discover: [
    __DIR__.'/../app-modules/*/src/Listeners',
])
```

This automatically discovers event listeners in all modules!

### Step 8: Custom Artisan Commands

To make module creation easier, we created custom Artisan commands:

**Create a module:**
```bash
php artisan make:module client
```

This command:
- Creates the directory structure
- Generates the ServiceProvider
- Creates base files (routes, etc.)

**Create module-specific classes:**
```bash
php artisan make:module-service client CreateClient
php artisan make:module-repository client ClientRepository
php artisan make:module-action client SendInvoiceEmail
```

These commands follow Laravel conventions and place files in the correct module directory.

## Part 3: Why This Approach is Better

### Advantages Over Package-Based Solutions

1. **No Magic, Full Control**
   - Everything is explicit and in your codebase
   - Easy to understand and modify
   - No hidden behavior

2. **Stays Close to Laravel**
   - Uses standard Laravel features (ServiceProviders, autoloading)
   - Follows Laravel conventions
   - Easy for Laravel developers to understand

3. **Less Dependencies**
   - One less package to maintain
   - No version conflicts
   - No breaking changes from package updates

4. **Flexibility**
   - Customize the structure to your needs
   - Add features specific to your use case
   - No constraints from package limitations

5. **Learning Value**
   - Understand how autoloading works
   - Understand ServiceProvider registration
   - Better understanding of Laravel internals

### Trade-offs

**Cons:**
- You maintain the code (but it's simple!)
- Need to understand autoloading and ServiceProviders
- Slightly more setup than using a package

**Pros (outweigh cons):**
- Full control
- No external dependencies
- Better understanding of your architecture
- Easier to customize

## Summary

**Why Modular Monolith?**
- Solves scaling and organization problems
- Enables parallel development
- Maintains deployment simplicity
- Provides migration path to microservices

**How We Handle Modules:**
1. Custom directory structure (`app-modules/`)
2. PSR-4 autoloading in `composer.json`
3. Custom autoloader for case-sensitivity
4. Auto-discovery via `ModuleServiceProvider`
5. Each module has its own ServiceProvider
6. Custom Artisan commands for module creation

**Why No Packages?**
- Full control and flexibility
- Stays close to Laravel conventions
- Less dependencies
- Better learning experience
- No magic, everything is explicit

## Next Episode Preview

In the next episode, we'll dive into:
- Module structure and organization
- How modules communicate (Contracts, DTOs, Events)
- Enforcing boundaries with architecture tests

Thanks for watching! See you next time!
