<?php

namespace Zolta\Domain\ValueObjects;

use DateTimeImmutable;
use DateTimeInterface;
use Zolta\Domain\Attributes\Transform;
use Zolta\Domain\Attributes\UseInvariant;
use Zolta\Domain\Attributes\UsePolicy;
use Zolta\Domain\Attributes\UseRule;
use Zolta\Domain\Attributes\UseSpecification;
use Zolta\Domain\Invariants\EmailVOInvariant;
use Zolta\Domain\Policies\EmailPolicy;
use Zolta\Domain\Rules\MaxLengthRule;
use Zolta\Domain\Rules\NonEmptyRule;
use Zolta\Domain\Specifications\AllowedDomainSpecification;
use Zolta\Domain\Specifications\EmailFormatSpecification;
use Zolta\Domain\Transformers\DateTimeNormalizer;
use Zolta\Domain\Transformers\EmailNormalizer;

/**
 * Email VO — shows attribute-driven pipeline:
 *  - parameter-level transforms, rules, specifications
 *  - class-level invariants and policies (with options)
 *
 * Demonstrates:
 *  - options passed to attributes
 *  - conditional when example on a parameter (set in attributes)
 */
#[UseInvariant(EmailVOInvariant::class, ['allowFutureVerified' => false, 'domainRequiresVerified' => ['secure.example.com']])]
#[UsePolicy(EmailPolicy::class, ['requireVerified' => false, 'trustedDomains' => ['trusted.com', 'company.com']])]
final class EmailVo extends ValueObject
{
    public function __construct(
        #[Transform(EmailNormalizer::class, ['lowercase' => true, 'trim' => true])]
        #[UseRule(NonEmptyRule::class)]
        #[UseRule(MaxLengthRule::class, ['max' => 254])]
        #[UseSpecification(EmailFormatSpecification::class)]
        #[UseSpecification(AllowedDomainSpecification::class, ['allowed' => ['trusted.com', 'company.com', 'example.com'], 'when' => ['param' => 'address', 'op' => 'notNull']])]
        private string $address,

        #[Transform(DateTimeNormalizer::class, ['format' => null])]
        private ?DateTimeImmutable $verifiedAt = null,
        ?VOConstructionContext $context = null
    ) {
        $resolved = self::resolveInternal(['address' => $address, 'verifiedAt' => $verifiedAt], $context);
        $this->address = $resolved['address'];
        $this->verifiedAt = $resolved['verifiedAt'];
    }

    public function isVerified(): bool
    {
        if ($this->verifiedAt === null) {
            return false;
        }

        return $this->verifiedAt <= new DateTimeImmutable;
    }

    public function equals(\Zolta\Domain\Interfaces\VO $other): bool
    {
        return $other instanceof self && $this->address === $other->get('address');
    }

    public function toArray(): array
    {
        return [
            'address' => $this->address,
            'verifiedAt' => $this->verifiedAt?->format(DateTimeInterface::ATOM) ?? null,
        ];
    }
}
