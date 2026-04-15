# Zoltasoft Forge

**Zoltasoft Forge** is the domain foundation package of the Zoltasoft ecosystem.  
It gives you the core DDD building blocks (Value Objects, Entities/Aggregates, Rules, Specifications, Policies, Invariants, Transformers) so domain rules stay explicit, consistent, and framework-agnostic.

## Why this package exists

Forge centralizes domain behavior so application and transport layers can stay thin:

- **CQRS** consumes Forge to model command/query input as safe domain types.
- **HTTP** consumes Forge to map validated request data into domain objects.
- Domain constraints are enforced once, close to the model, instead of duplicated across handlers/controllers.

## Primary use case

Use Forge when you want to:

- protect domain invariants at construction time
- resolve domain objects from raw payloads (`::resolve(...)`)
- keep domain code independent from Laravel/Symfony adapters

## Installation

```bash
composer require zoltasoft/forge
```

Laravel adapter discovery is automatic through Composer metadata.

## Example: domain use case (Value Object resolution)

```php
use Zolta\Domain\Attributes\Transform;
use Zolta\Domain\Attributes\UseRule;
use Zolta\Domain\Rules\NonEmptyRule;
use Zolta\Domain\Transformers\EmailNormalizer;
use Zolta\Domain\ValueObjects\ValueObject;

final class Email extends ValueObject
{
    public function __construct(
        #[Transform(EmailNormalizer::class, ['lowercase' => true])]
        #[UseRule(NonEmptyRule::class)]
        public readonly string $address,
    ) {}
}

$email = Email::resolve(['address' => '  John@Example.com  ']);
// $email->address === "john@example.com"
```

## Example: runtime adapter discovery and binding resolution

```php
use Psr\Log\LoggerInterface;
use Zolta\Framework\FrameworkBootstrap;
use Zolta\Framework\FrameworkRegistry;
use Zolta\Support\ContainerRegistry;

FrameworkBootstrap::boot(); // discovers adapters from composer extra metadata
ContainerRegistry::set(app()); // Laravel container (or your PSR-11 container)

$concrete = FrameworkRegistry::resolveBinding(LoggerInterface::class);
$logger = ContainerRegistry::resolve(LoggerInterface::class);
```

This is the same runtime discovery model used by `zoltasoft/cqrs` and `zoltasoft/http` to resolve framework bindings without coupling domain code to a specific framework.

## Tests

```bash
composer run lint:test
composer run analyse
composer run md
composer run rector
composer run test
composer run qa
```

Monorepo runner:

```bash
./scripts/run-package-tests.sh packages/forge qa
```

## Collaboration

1. Keep Forge focused on domain concerns only.
2. Put integration/runtime concerns in adapters or consumer packages.
3. Run `composer run qa` before opening a PR.

## Ecosystem links

- **Forge (Domain):** `zoltasoft/forge`
- **CQRS (Application):** [`zoltasoft/cqrs`](../cqrs)
- **HTTP (Transport/API):** [`zoltasoft/http`](../http)
- **Forge docs:** [`docs/`](./docs/)

## License

**Proprietary — © 2025 Redouane Taleb**  
See [`LICENSE`](./LICENSE).
