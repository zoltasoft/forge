# Zolta Forge

[![PHP Version](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net/)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-Level%206-brightgreen.svg)](https://phpstan.org/)
[![Laravel Version](https://img.shields.io/badge/Laravel-10+-red.svg)](https://laravel.com/)
[![License](https://img.shields.io/badge/License-Proprietary-orange.svg)](LICENSE)

**Zolta Forge** is the domain layer foundation of the Zolta framework. It provides Value Objects with attribute-based validation pipelines, Entities, Aggregate Roots, Rules, Specifications, Policies, Invariants, Transformers, a structured exception hierarchy, and a framework-agnostic adapter system — all enforced by PHPStan Level 6.

---

## Features

- **Value Objects** — Immutable domain primitives with automatic construction via `resolve()`, nested VO support, and reflection-cached pipelines
- **Attribute-Based Validation** — `#[UseRule]`, `#[UseSpecification]`, `#[UsePolicy]`, `#[UseInvariant]`, `#[Transform]` applied directly on constructor parameters
- **Entities & Aggregates** — `Entity` base class with `create()`/`restore()` factories; `AggregateRoot` with domain event recording
- **Rules** — Pre-construction validation: `NonEmptyRule`, `MinLengthRule`, `MaxLengthRule`, `RegexRule`, `RangeRule`, `AllowedValuesRule`, `TypeRule`
- **Specifications** — Boolean-result domain predicates: `UniqueSpecification`, `ExistsSpecification`, `IsActiveSpecification`, `CompositeSpecification`
- **Policies** — Authorization/business policy enforcement: `OwnershipPolicy`, `RateLimitPolicy`, `FeatureFlagPolicy`
- **Invariants** — Post-construction consistency checks: `StateInvariant`, `CrossFieldInvariant`
- **Transformers** — Input normalization before validation: `EmailNormalizer`, `DateTimeNormalizer`, `IdentifierNormalizer`
- **Exception Hierarchy** — `BaseException` → `RestApiException` → HTTP-specific exceptions (400, 401, 403, 404, 409, 422, 500) with `RenderableExceptionInterface`
- **DTO System** — `InputDTO` / `ResponseDTO` interfaces with `Normalizable` trait for consistent data transfer
- **Framework Adapter** — `FrameworkBootstrap` with Composer-based discovery, `FrameworkRegistry` for priority-based adapter binding
- **Container** — `ZoltaForgeContainer` (PSR-11) with `ContainerRegistry` and global helpers (`app()`, `logger()`)
- **VOConstructionContext** — Runtime overrides for rules, policies, and normalizers per-request without closures

---

## Install

```bash
composer require zolta/forge
```

Laravel auto-discovers the service provider. No manual registration needed.

---

## Quick Start

### 1. Define a Value Object

```php
namespace App\Domain\ValueObjects;

use Zolta\Domain\Attributes\Transform;
use Zolta\Domain\Attributes\UseRule;
use Zolta\Domain\ValueObjects\ValueObject;
use Zolta\Domain\Rules\NonEmptyRule;
use Zolta\Domain\Rules\MaxLengthRule;
use Zolta\Domain\Transformers\EmailNormalizer;

final class Email extends ValueObject
{
    public function __construct(
        #[Transform(EmailNormalizer::class, ['lowercase' => true])]
        #[UseRule(NonEmptyRule::class)]
        #[UseRule(MaxLengthRule::class, ['max' => 256])]
        protected string $address,

        protected ?DateTimeImmutable $verifiedAt = null,
    ) {}
}
```

### 2. Resolve a Value Object

```php
// From raw data — rules, transformers, specs all execute automatically
$email = Email::resolve(['address' => '  John@Example.COM  ']);

// Access values
$email->address();       // "john@example.com" (normalized)
$email->verifiedAt();    // null

// Equality
$email->equals($other);  // compares all properties

// Serialization
$email->toArray();       // ['address' => 'john@example.com', 'verifiedAt' => null]
```

### 3. Built-in UUID Value Objects

```php
use Zolta\Domain\ValueObjects\UuidValueObject;

final class UserId extends UuidValueObject {}

$id = UserId::generate();    // new UUID v4
$id = UserId::fromString('550e8400-e29b-41d4-a716-446655440000');
$id->toString();             // "550e8400-..."
```

### 4. Define an Entity

```php
use Zolta\Domain\Entities\Entity;

final class User extends Entity
{
    private function __construct(
        private UserId $id,
        private Username $name,
        private Email $email,
        private HashedPassword $password,
    ) {}

    public static function create(
        Username $name,
        Email $email,
        HashedPassword $password,
    ): static {
        $user = new static(
            id: UserId::generate(),
            name: $name,
            email: $email,
            password: $password,
        );
        $user->recordEvent(new UserCreatedEvent($user->id));
        return $user;
    }

    public static function restore(
        UserId $id,
        Username $name,
        Email $email,
        HashedPassword $password,
    ): static {
        return new static($id, $name, $email, $password);
    }
}
```

### 5. Domain Event Recording (AggregateRoot)

```php
use Zolta\Domain\Entities\AggregateRoot;

final class User extends AggregateRoot
{
    // ... same as Entity but with event support built in

    public function changeEmail(Email $newEmail): void
    {
        $this->email = $newEmail;
        $this->recordEvent(new UserEmailChangedEvent($this->id, $newEmail));
    }
}

// After persistence
$events = $user->releaseEvents(); // returns and clears recorded events
```

### 6. Compose Validation Attributes

```php
final class Username extends ValueObject
{
    public function __construct(
        #[UseRule(NonEmptyRule::class)]
        #[UseRule(MinLengthRule::class, ['min' => 3])]
        #[UseRule(MaxLengthRule::class, ['max' => 50])]
        #[UseRule(RegexRule::class, ['pattern' => '/^[a-zA-Z0-9_]+$/'])]
        #[UseSpecification(UniqueSpecification::class)]
        #[UsePolicy(RateLimitPolicy::class, ['max' => 5, 'window' => 3600])]
        #[UseInvariant(StateInvariant::class)]
        protected string $value,
    ) {}
}
```

### 7. Runtime Overrides with VOConstructionContext

```php
use Zolta\Domain\ValueObjects\VOConstructionContext;

$context = new VOConstructionContext(
    skipRules: [MaxLengthRule::class],          // disable specific rules
    additionalRules: [CustomRule::class],       // add rules at runtime
    ruleOverrides: ['max' => 500],             // override rule parameters
);

$email = Email::resolve(['address' => $input], $context);
```

---

## Exception Hierarchy

```
BaseException
└─ RestApiException
    ├─ BadRequestException          (400)
    ├─ UnauthorizedException        (401)
    ├─ ForbiddenException           (403)
    ├─ NotFoundException            (404)
    ├─ ConflictException            (409)
    ├─ ValidationException          (422)
    └─ InternalServerErrorException (500)
```

All exceptions implement `RenderableExceptionInterface` for consistent JSON API responses:

```php
throw new NotFoundException('User not found', ['userId' => $id]);
// → { "success": false, "message": "User not found", "errors": { "userId": "..." } }
```

---

## VO Resolution Pipeline

When `Email::resolve([...])` is called, the following stages execute in order:

```
1. Raw Input
2. Transform        → #[Transform] normalizers run (EmailNormalizer, etc.)
3. Rules            → #[UseRule] validation (NonEmptyRule, MaxLengthRule, etc.)
4. Specifications   → #[UseSpecification] predicates (UniqueSpecification, etc.)
5. Policies         → #[UsePolicy] authorization checks (OwnershipPolicy, etc.)
6. Construction     → VO instantiated via constructor
7. Invariants       → #[UseInvariant] post-construction checks (StateInvariant, etc.)
```

All stages are reflection-cached for performance after the first resolution.

---

## Ecosystem

| Package | Layer | Description |
|---------|-------|-------------|
| **zolta/forge** | **Domain** | **Value Objects, Entities, Rules, Specifications, Policies, Invariants** |
| [zolta/cqrs](../zolta-cqrs) | Application | Commands, Queries, Events, Repositories, Transactions |
| [zolta/http](../zolta-http) | API | Routing, Request/Response, Authorization |

---

## QA

This package provides a `Makefile` for running quality checks both standalone and within the monorepo.

```bash
make qa                              # Full QA suite (lint + analyse + md + rector + test)
make run SCRIPT=analyse              # Single script
make run SCRIPT=qa KEEP=keep         # Keep vendor/ between runs
```

Monorepo runner:

```bash
./scripts/run-package-tests.sh packages/zolta-forge qa
```

Individual tools:

```bash
composer run lint        # Pint code style
composer run analyse     # PHPStan Level 6
composer run md          # PHPMD
composer run rector      # Rector
composer run test        # PHPUnit
```

---

## Documentation

Full documentation is available in the [`docs/`](./docs/) directory, organized for serving via Nuxt Content.

---

## License

**Proprietary — © 2025 Redouane Taleb**
Unauthorized copying, modification, or distribution is prohibited.

Thank you for your support!
