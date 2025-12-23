# Scaling with Modular Monolith

## Parallel Development

Multiple teams can work on different modules simultaneously:

```
Team A: Working on Invoice module
Team B: Working on Payment module  
Team C: Working on Reporting module
```

No conflicts because each module is self-contained.

### Working with Contracts

While one team implements a service, another can use a fake implementation:

```php
// PaymentServiceProvider
public function register(): void
{
    // During development, use fake implementation
    $this->app->bind(
        PaymentContract::class,
        FakePaymentService::class
    );
    
    // Later, switch to real implementation
    // $this->app->bind(PaymentContract::class, StripePaymentService::class);
}
```

## Easy Onboarding

New developers can:
- Focus on one module at a time
- Understand the entire feature (all code is in one place)
- See examples in other modules

## Incremental Complexity

Start simple, add complexity as needed:

1. **Phase 1**: Simple CRUD (Controller → Service → Repository → Model)
2. **Phase 2**: Add business logic (Actions)
3. **Phase 3**: Add orchestration (Services)
4. **Phase 4**: Add background jobs (Jobs)
5. **Phase 5**: Add events for inter-module communication

## Migration Path to Microservices

If you ever need to split into microservices:

1. Each module is already a bounded context
2. Communication is already contract-based
3. Events are already decoupled
4. Extract the module into a separate service
5. Replace contracts with HTTP APIs or message queues

## Performance Considerations

### Trade-offs

- **Cross-module data retrieval**: Cannot use Eloquent eager loading across modules
- **Deployment**: Modules cannot be deployed independently (still a monolith)

### Solutions

- Use deferred props (Inertia 2.0) for async data loading
- Configure load balancer to route module-specific traffic to dedicated servers
- Prefetch pages before user visits them

## Next Steps

- [Creating a Module](./creating-a-module.md) - Step-by-step guide
- [Naming Conventions](./naming-conventions.md) - Code style guide
