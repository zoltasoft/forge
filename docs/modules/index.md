---
title: Modules
description: Overview of all Zolta Forge modules.
navigation:
  title: Modules
  order: 2
---

# Modules

Zolta Forge is organized into focused modules. Each module addresses a specific domain modeling concern, and they compose naturally together.

## Domain modeling stack

```
  ┌──────────────────────────────────────────────┐
  │            #[Transform]                       │
  │   Normalize input (trim, lowercase, etc.)     │
  └──────────────┬───────────────────────────────┘
                 ▼
  ┌──────────────────────────────────────────────┐
  │            #[UseRule]                          │
  │   Guard constraints (non-empty, max length)   │
  └──────────────┬───────────────────────────────┘
                 ▼
  ┌──────────────────────────────────────────────┐
  │         #[UseSpecification]                   │
  │   Business specs (email format, UUID, age)    │
  └──────────────┬───────────────────────────────┘
                 ▼
  ┌──────────────────────────────────────────────┐
  │          Nested VO Resolution                 │
  │   Recursively resolve child Value Objects     │
  └──────────────┬───────────────────────────────┘
                 ▼
  ┌──────────────────────────────────────────────┐
  │         #[UseInvariant]                       │
  │   Class-level structural guarantees           │
  └──────────────┬───────────────────────────────┘
                 ▼
  ┌──────────────────────────────────────────────┐
  │          #[UsePolicy]                         │
  │   Post-construction behavioral logic          │
  └──────────────────────────────────────────────┘
```

## Module index

| Module | Namespace | Purpose |
|--------|-----------|---------|
| [Value Objects](/forge/modules/value-objects) | `Zolta\Domain\ValueObjects` | Immutable domain primitives with resolution pipeline |
| [Entities & Aggregates](/forge/modules/entities) | `Zolta\Domain\Entities`, `Zolta\Domain\Aggregates` | Mutable domain objects with event recording |
| [Rules](/forge/modules/rules) | `Zolta\Domain\Rules` | Guard-style input validation |
| [Specifications](/forge/modules/specifications) | `Zolta\Domain\Specifications` | Boolean business rule evaluation |
| [Policies](/forge/modules/policies) | `Zolta\Domain\Policies` | Post-construction behavior and side effects |
| [Invariants](/forge/modules/invariants) | `Zolta\Domain\Invariants` | Class-level structural enforcement |
| [Transformers](/forge/modules/transformers) | `Zolta\Domain\Transformers` | Input normalization before validation |
| [Exceptions](/forge/modules/exceptions) | `Zolta\Domain\Exceptions`, `Zolta\Exceptions` | Domain and REST API exception hierarchy |
| [Framework Adapter](/forge/modules/framework) | `Zolta\Framework` | Auto-discovery and runtime framework binding |
| [DTO System](/forge/modules/dto) | `Zolta\Support\Application\DTO` | Typed Input and Response DTOs |
| [Container & Registry](/forge/modules/container) | `Zolta\Support` | PSR-11 container bridge and service resolution |

## Composition operators

All domain building blocks support fluent composition:

```php
// Rules: sequential AND, fallback OR
$rule = (new NonEmptyRule())->and(new MaxLengthRule());

// Specifications: logical AND, logical OR
$spec = (new EmailFormatSpec())->and(new AllowedDomainSpec());

// Policies: both run
$policy = (new EmailPolicy())->and(new AuditPolicy());

// Transformers: pipe (a → b → c)
$transformer = (new TrimTransformer())->and(new LowercaseTransformer());

// Invariants: AND / OR
$invariant = (new CreditInvariant())->and(new BalanceInvariant());
```
