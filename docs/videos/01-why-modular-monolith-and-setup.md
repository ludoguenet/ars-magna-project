# Episode 1: Why Modular Monolith & Setup (2-3 min)

## Introduction

Welcome! Today we'll cover why we chose a Modular Monolith architecture and how we implemented it **without third-party packages**.

## Why Modular Monolith?

Traditional Laravel structure works great for small apps, but as you grow:
- **Code scatters** across directories (models here, controllers there)
- **Parallel development** becomes difficult with merge conflicts
- **No clear boundaries** between features
- **Scaling** becomes a nightmare with 50+ models

**Modular Monolith** solves this by keeping one codebase but enforcing **strict boundaries** between business domains. Benefits:
- ✅ Simple deployment (one codebase)
- ✅ Parallel development (teams work independently)
- ✅ Easy onboarding (focus on one module at a time)
- ✅ Migration path to microservices later

## Our Custom Implementation

We're **not using packages** like `nwidart/laravel-modules`. Why? Full control, stay close to Laravel conventions, no magic.

### Setup (3 steps)

**1. Directory Structure**
All modules live in `app-modules/`:
```
app-modules/
├── client/
│   ├── src/          # Controllers, Models, Services
│   ├── routes/
│   └── database/
```

**2. Composer Autoloading**
Add to `composer.json`:
```json
{
    "autoload": {
        "psr-4": {
            "AppModules\\": "app-modules/"
        }
    }
}
```
Run `composer dump-autoload`.

**3. Module Discovery**
`ModuleServiceProvider` auto-discovers and registers all modules. Each module has its own `ServiceProvider` that loads routes, migrations, and views.

## Contracts & Dependency Inversion

**Critical principle:** Modules communicate through **Contracts** (interfaces), never direct dependencies.

### The Problem Without Contracts

```php
// ❌ BAD: Direct dependency breaks boundaries
class BookingController
{
    public function create(Request $request)
    {
        $flight = Flight::find($request->flight_id); // Violates boundary!
    }
}
```

This creates tight coupling. If Flight module changes, Booking breaks.

### The Solution: Contracts

**Contracts** are PHP interfaces that define the public API between modules. They enforce **Dependency Inversion Principle**: depend on abstractions, not concretions.

```php
// ✅ GOOD: Flight module defines the contract
namespace AppModules\Flight\src\Contracts;

interface FlightRepositoryContract
{
    public function find(int $flightId): FlightDTO;
}

// Flight module implements it
class FlightRepository implements FlightRepositoryContract { }

// Booking module uses the contract (not the implementation!)
class BookingController
{
    public function __construct(
        private FlightRepositoryContract $flightRepository  // Interface!
    ) {}
}
```

**Binding in ServiceProvider:**
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

### Why This Matters

- ✅ **Loose coupling** - Modules depend on interfaces, not implementations
- ✅ **Testability** - Easy to mock contracts in tests
- ✅ **Flexibility** - Swap implementations without breaking consumers
- ✅ **Boundaries enforced** - Architecture tests prevent direct model access

## Summary

**Why Modular Monolith?** Solves scaling, enables parallel development, maintains simplicity.

**How?** Custom `app-modules/` structure, PSR-4 autoloading, auto-discovery.

**Key Rule:** Always use **Contracts** for cross-module communication. Never break dependency inversion by depending on concrete classes.

Next episode: Module structure and cross-module communication patterns!
