# Introduction to Modular Monolith

This application uses a **Modular Monolith** architecture inspired by Ryuta Hamasaki (Laracon India 2025). It's a middle ground between a traditional monolith and microservices.

## What is a Modular Monolith?

A **Modular Monolith** keeps everything in one codebase (easy deployment, ACID transactions) but enforces **strict boundaries** between business domains. Each module is self-contained with its own models, controllers, views, and business logic.

## Why Use It?

### Problems with Traditional Laravel Structure

As applications grow, the traditional structure becomes a bottleneck:

- ❌ Business logic scattered across controllers
- ❌ Hard to find related code (models, controllers, views are separated)
- ❌ Difficult to work in parallel (multiple developers touching the same files)
- ❌ No clear boundaries between features
- ❌ Testing becomes harder as dependencies grow

### Benefits of Modular Monolith

- ✅ **Simple deployment** - One codebase, one deployment pipeline
- ✅ **ACID transactions** - Can use database transactions across modules
- ✅ **Well-defined boundaries** - Each module is isolated
- ✅ **Parallel development** - Teams can work on different modules
- ✅ **Easy onboarding** - New developers can focus on one module
- ✅ **Migration path** - Can extract modules to microservices later

## When to Use It?

**Don't use it for simple or small apps** - it's overengineering. Start with a traditional monolith and migrate to modular monolith when you start feeling the pain of a monolith.

## Real-World Examples

- **Shopify** - One of the largest Rails applications, migrated to modular monolith
- **GitLab** - Massive codebase with 2M+ lines of Ruby code
- **Artisan Airlines** - Demo application by Ryuta Hamasaki (Laracon India 2025)

## Next Steps

- [Module Structure](./02-module-structure.md) - How modules are organized
- [Cross-Module Communication](./03-cross-module-communication.md) - Contracts, DTOs, Events
- [Creating a Module](./creating-a-module.md) - Step-by-step guide
