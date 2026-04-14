# Zolta Forge

[![PHP Version](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net/)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-Level%206-brightgreen.svg)](https://phpstan.org/)
[![Laravel Version](https://img.shields.io/badge/Laravel-10+-red.svg)](https://laravel.com/)
[![Symfony Version](https://img.shields.io/badge/Symfony-7.4+-black.svg)](https://symfony.com/)
[![Tests](https://img.shields.io/badge/Tests-25%20passing-green.svg)]()
[![License](https://img.shields.io/badge/License-Proprietary-orange.svg)](LICENSE)

**Zolta Forge** is a production-grade **Hexagonal Architecture** framework for PHP — the first to bring **Domain-Driven Design (DDD)** and **CQRS** to Laravel and Symfony simultaneously with enterprise-grade type safety (PHPStan Level 6).

Build scalable, maintainable applications with **strict domain isolation**, **automatic Value Object hydration**, **framework-agnostic core**, and **minimal boilerplate** — all with zero compromise on architectural integrity.

---

## 🎯 The Problem Zolta Solves

Most frameworks force a choice:

- **Laravel** 💡: Great for rapid development, but architectural freedom means teams drift into spaghetti code
- **Symfony** 🛡️: Excellent structure, but boilerplate-heavy and learning curve is steep
- **Neither** 😞: True hexagonal architecture with DDD/CQRS is possible but requires engineering discipline and constant vigilance

**Zolta Forge** makes hexagonal architecture the **default**, not an after-thought. Your code _cannot_ violate domain boundaries — PHPStan Level 6 and strict typing enforce it.

---

## 🚀 Features That Matter

### **Hexagonal Architecture (Built-In)**

- ✅ **Domain-First Design** — Domain layer has zero framework dependencies; protected by strict types (PHPStan L6).
- ✅ **Clear Layer Separation** — API → Application → Domain → Infrastructure with automatic dependency inversion.
- ✅ **Pluggable Adapters** — Works seamlessly with Laravel Eloquent **or** Symfony Doctrine; swap persistence/events without touching domain code.
- ✅ **Framework-Agnostic Core** — Same business logic runs on Laravel and Symfony simultaneously.

### **Value Object Hydration (Unique to Zolta)**

- ✅ **Auto VO Construction** — Build complex Value Objects with nested schemas directly from HTTP requests via `Cqrs::make()`.
- ✅ **Runtime-Safe Overrides** — Adapt validation rules and normalizers per-request via `VOConstructionContext` without modifying VO classes.
- ✅ **Strict Invariant Enforcement** — Domain rules (policies, specifications, constraints) validated at VO construction — **impossible to violate invariants**.

### **Real CQRS Engine**

- ✅ **Commands & Queries** with automatic handler discovery via `#[HandlesCommand]` / `#[HandlesQuery]` attributes.
- ✅ **Intelligent Results** — Typed `Result<T>` and `Option<T>` wrappers for safe, composable error handling.
- ✅ **Pipeline Orchestration** — `ApplicationService` chains commands/queries with automatic mapping and validation.

### **Developer Experience**

- ✅ **Attribute-Driven Controllers** — Zero controller code; routing, validation, service execution, response mapping all declarative.
- ✅ **Real-Time Dev Mode** — `zolta:dev` watches your code and auto-rebuilds routes, CQRS maps, and reflection cache (backend HMR).
- ✅ **DTO-Driven Validation** — Request → DTO → Service pipeline is type-safe and consistent.

### **Data & Persistence**

- ✅ **Advanced Repository Layer** — Filtering, sorting, pagination, relation eager-loading, streaming support, intelligent caching.
- ✅ **Multi-ORM Support** — Eloquent (Laravel) and Doctrine (Symfony) adapters; extend with custom ORMs.

### **Production Ready**

- ✅ **Intelligent Caching** — CQRS map caching, attribute route caching, reflection caching with auto-refresh in dev mode.
- ✅ **Centralized Exception Handling** — Consistent API error responses with structured payloads.
- ✅ **Type-Safe End-to-End** — PHPStan Level 6 enforces correctness at compile time (25 tests, 78 assertions, 100% passing).

---

## ✨ What Makes Zolta Forge Unique

| Capability                 | Zolta Forge                  | Laravel                | Symfony                    |
| -------------------------- | ---------------------------- | ---------------------- | -------------------------- |
| **Hexagonal Architecture** | ✅ Built-in                  | ❌ Manual              | ✅ Possible but manual     |
| **Multi-Framework Core**   | ✅ Laravel + Symfony         | ➖ Laravel only        | ➖ Symfony only            |
| **Auto VO Hydration**      | ✅ With runtime overrides    | ❌ Manual DTO mapping  | ❌ Manual entity hydration |
| **CQRS Engine**            | ✅ Full pipeline             | ❌ Third-party package | ✅ Possible but verbose    |
| **Attribute Routing**      | ✅ Zero controller code      | ✅ Possible            | ✅ Possible                |
| **PHPStan Level 6**        | ✅ 100% passing              | ⚠️ Partial             | ⚠️ Partial                 |
| **Type-Safe Results**      | ✅ `Result<T>` + `Option<T>` | ❌ Exceptions only     | ❌ Exceptions only         |
| **Domain Event Handling**  | ✅ First-class               | ⚠️ Manual              | ✅ Messenger (complex)     |

**Bottom line:** Zolta Forge is the **only** framework that gives you production-grade hexagonal architecture with DDD/CQRS **out of the box** across both Laravel and Symfony, enforced by strict typing.

---

## 📦 Installation

```bash
composer require zolta/forge
```

**Nothing else needed** — Zolta Forge configures itself automatically for Laravel or Symfony.

To customize configuration:

```bash
# Laravel
php artisan vendor:publish --provider="Zolta\Laravel\Providers\ZoltaServiceProvider" --tag=config

# Symfony
# Configuration is automatic via Symfony's config loading
```

---

## ⚙️ Quick Start (Full End-to-End Example)

### Step 1: Define Domain Value Object

```php
namespace App\Domain\ValueObjects;

use Zolta\Core\Domain\Attributes\Transform;
use Zolta\Core\Domain\Attributes\UseRule;
use Zolta\Core\Domain\Attributes\UseInvariant;

final class Email extends ValueObject
{
    public function __construct(
        #[Transform(EmailNormalizer::class, ['lowercase' => true])]
        #[UseRule(NonEmptyRule::class)]
        #[UseRule(MaxLengthRule::class, ['max' => 256])]
        protected string $address,

        protected ?DateTimeImmutable $verifiedAt = null,
        protected ?VOConstructionContext $context = null
    ) {}
}
```

### Step 2: Create Typed Command (VOs Carry Domain Meaning)

```php
namespace App\Application\Commands\Users;

final class RegisterUserCommand extends Command
{
    public function __construct(
        public readonly Email $email,
        public readonly Username $username,
        public readonly string $password,
        public readonly RoleId $roleId,
    ) {}
}
```

### Step 3: Validator with Policy

```php
#[ValidatesCommand(RegisterUserCommand::class)]
final class RegisterUserCommandValidator
{
    public function __construct(private UserPolicy $policy) {}

    public function __invoke(RegisterUserCommand $command): Result
    {
        $this->policy->assertCanRegister($command->email);
        return Result::success();
    }
}
```

### Step 4: Handler with Domain Logic

```php
#[HandlesCommand(RegisterUserCommand::class)]
final class RegisterUserCommandHandler
{
    public function __invoke(
        RegisterUserCommand $command,
        UserRepository $repo,
        UserFactory $factory
    ): Result {
        $user = $factory->create(
            email: $command->email,
            username: $command->username,
            password: $command->password,
        );
        $repo->saveUser($user);
        return Result::success(new UserPayload($user), $user->releaseEvents());
    }
}
```

### Step 5: Application Service (Orchestration)

```php
final class RegisterService
{
    public function __construct(private ApplicationService $pipeline) {}

    public function __invoke(RegisterDTO $dto): RegisterResponseDTO
    {
        $result = $this->pipeline
            ->runAndCapture(RegisterUserCommand::class, [
                'email' => $dto->email,
                'username' => $dto->username,
                'password' => $dto->password,
                'roleId' => $roleId,
            ])
            ->getOrFail(fn() => new RuntimeException('Registration failed'));

        return RegisterResponseDTO::fromDomain($result['user']);
    }
}
```

### Step 6: API Controller (Zero Boilerplate)

```php
#[Route('auth/register', methods: ['POST'], middleware: ['api'], name: 'auth.register')]
#[Request(RegisterRequest::class, RegisterDTO::class)]
#[Service(RegisterService::class, 'Registration successful.', 201)]
#[Response(RegisterResource::class)]
final class RegisterController extends Controller {}
```

**That's the entire flow:**

- HTTP POST → `RegisterRequest` validates input → `RegisterDTO` carries data → `RegisterService` orchestrates → `RegisterUserCommand` built with VOs auto-hydrated → validator checks policy → handler creates aggregate → `UserPayload` returned → `RegisterResource` formats response.
- **All type-safe. All domain-enforced. Zero controller boilerplate.**

### Step 7: Dev Mode (Recommended)

```bash
php artisan zolta:dev
```

- Watches your services
- Auto-rebuilds routes, CQRS maps, reflection cache
- Runs Laravel dev server
- Backend HMR-style experience ✨

---

## 🏗️ Hexagonal Architecture Visualized

```
┌─────────────────────────────────────────────────────────────────┐
│                         API Layer (Entry)                       │
│          Controllers, Requests, Resources (Attributes)          │
└────────────────────────┬────────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────────┐
│                    Application Layer                            │
│   Commands, Queries, Handlers, Results, Pipelines (Orchestration) │
└────────────────────────┬────────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────────┐
│                  Domain Layer (Core - Isolated)                 │
│   Aggregates, Entities, VOs, Events, Rules, Policies (Pure)     │
│              ← Zero Framework Dependencies →                    │
│                Enforced by PHPStan Level 6                      │
└────────────────────────┬────────────────────────────────────────┘
                         │
        ┌────────────────┴────────────────┐
        │                                 │
┌───────▼──────────────┐      ┌──────────▼────────────┐
│  Infrastructure:     │      │ Infrastructure:       │
│   Laravel Adapter    │      │  Symfony Adapter      │
│  ┌──────────────┐    │      │  ┌──────────────┐     │
│  │   Eloquent   │    │      │  │   Doctrine   │     │
│  │ Repositories │    │      │  │ Repositories │     │
│  └──────────────┘    │      │  └──────────────┘     │
│  ┌──────────────┐    │      │  ┌──────────────┐     │
│  │  Event Bus   │    │      │  │Event Dispatch│     │
│  └──────────────┘    │      │  └──────────────┘     │
└──────────────────────┘      └──────────────────────┘
```

**Key principle:** All arrows point **inward**. Domain has no idea Laravel or Symfony exist.

---

## 🎯 When to Use Zolta Forge

**Perfect for:**

- 🚀 Microservices & distributed systems (hexagonal makes swapping services easy)
- 🧠 Complex domain logic (DDD/CQRS enforce clarity and safety)
- 👥 Multi-tenant SaaS platforms (domain isolation protects invariants)
- 🤝 Cross-team projects (clear contracts between layers reduce friction)
- 📚 Long-lived codebases (strict typing catches drift early)
- 📊 Data-heavy applications (event sourcing, audit trails, domain events)

**Consider alternatives if:**

- 🚀 Building throwaway prototypes (setup overhead not justified)
- 👶 Team unfamiliar with DDD/CQRS (learning curve ~ 2–4 weeks)
- 🏃 Simple CRUD (overkill for maintenance forms)

---

## 🏅 Production-Grade Standards

✅ **Code Quality**

- PHPStan Level 6 (strict types across 308 files, **zero errors**)
- 25+ unit tests with 78 assertions (**100% passing**)
- PHPMD, Rector, Pint compliance

✅ **Framework Support**

- Laravel 10+ (Eloquent, Sanctum, validation)
- Symfony 7.4+ (Doctrine, security, DependencyInjection)
- Proven working on both frameworks **simultaneously**

✅ **Type Safety**

- Strict nullable handling
- Generic constraints on Result/Option
- Dependency inversion enforced by types

✅ **Testing & CI**

- GitHub Actions workflow for package QA
- Integration tests across Laravel + Symfony
- Fuzzing-ready hydration system

---

## 📖 Documentation

- **[Full Architecture Guide](docs/architecture.md)** — Deep dive into hexagonal design
- **[VO Hydration Patterns](docs/vo-hydration.md)** — Safe, flexible Value Object construction
- **[CQRS Patterns](docs/cqrs.md)** — End-to-end command/query flows
- **[Real-World Examples](https://github.com/redouane-taleb/zoltatech-monorepo)**
  - `apps/interviewlike-server` — Full Laravel + Zolta app with UserManagementService
  - `apps/symphonyapp` — Same architecture, Symfony + Doctrine variant

---

## 🔧 Useful Commands

Usually not needed — Zolta rebuilds everything automatically.

Use only for debugging or CI/CD:

```bash
php artisan zolta:routes:cache
php artisan zolta:routes:clear

php artisan zolta:maps:cache --fresh
php artisan zolta:maps:clear
```

---

## 🧪 Developer: Running QA & Tests

This package provides a `Makefile` and is designed to work both inside the monorepo and as a standalone repository.

**Local (standalone or inside monorepo):**

```bash
make qa           # Run full QA pipeline
make run SCRIPT=analyse  # Run specific script
make run SCRIPT=qa KEEP=keep  # Keep vendor/ between runs
```

**Monorepo behavior:** When part of the full monorepo, the `Makefile` delegates to the centralized runner:

```bash
./scripts/run-package-tests.sh packages/zolta-forge qa
```

**Notes:**

- The Makefile falls back to local `composer install` if the monorepo runner is unavailable (standalone package cloning).
- CI uses GitHub Actions (`.github/workflows/package-qa.yml`).
- For isolated environments, consider Docker or the CI workflow.

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Add tests for new functionality
4. Ensure `make qa` passes (or use `./scripts/run-package-tests.sh packages/zolta-forge qa`)
5. Submit a PR

**Quality standards:** PHPStan L6, full test coverage, no architectural violations.

---

## 📄 License

**Proprietary** — © 2025 Redouane Taleb  
Unauthorized copying, modification, or distribution is prohibited.

---

## ⭐ Support

If Zolta Forge helps you build cleaner, more maintainable PHP applications, consider giving it a **GitHub Star** 🌟

**More documentation, examples, and tooling are coming soon.**

Thank you for your support!
