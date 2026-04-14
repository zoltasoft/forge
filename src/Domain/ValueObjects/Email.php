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
use Zolta\Domain\Transformers\DateTimeNormalizer;
use Zolta\Domain\Transformers\EmailNormalizer;

#[UsePolicy(EmailPolicy::class, [
    'requireVerified' => false,
    'trustedDomains' => ['gmail.com', 'protonmail.com'],
])]
#[UseInvariant(EmailVOInvariant::class, [
    'allowFutureVerified' => false,
    'domainRequiresVerified' => ['protonmail.com'],
])]
final class Email extends ValueObject
{
    protected array $getters = ['address', 'verifiedAt'];

    public function __construct(
        #[Transform(EmailNormalizer::class, ['lowercase' => true, 'trim' => true])]
        #[UseRule(NonEmptyRule::class)]
        #[UseRule(MaxLengthRule::class, ['max' => 256])]
        #[UseSpecification(AllowedDomainSpecification::class, ['allowed' => ['gmail.com', 'protonmail.com']])]
        protected string $address,

        #[Transform(DateTimeNormalizer::class)]
        protected ?DateTimeImmutable $verifiedAt = null,

        protected ?VOConstructionContext $context = null
    ) {
        parent::__construct();
    }

    public function isVerified(): bool
    {
        return $this->verifiedAt !== null && $this->verifiedAt <= new DateTimeImmutable;
    }

    public function toArray(): array
    {
        return [
            'address' => $this->address,
            'verifiedAt' => $this->verifiedAt?->format(DateTimeInterface::ATOM),
        ];
    }

    /**
     * Determines whether this Email value object is equal to another.
     */
    public function equals(mixed $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        // Normalize both addresses to avoid case sensitivity issues
        $addr1 = strtolower(trim($this->address));
        $addr2 = strtolower(trim($other->address));

        $verified1 = $this->verifiedAt?->format(DateTimeInterface::ATOM);
        $verified2 = $other->verifiedAt?->format(DateTimeInterface::ATOM);

        return $addr1 === $addr2 && $verified1 === $verified2;
    }
}
