<?php

namespace Zolta\Domain\ValueObjects;

use DateTimeImmutable;
use Zolta\Domain\Interfaces\VO;

final class AccessToken extends ValueObject
{
    protected array $getters = ['token', 'expiresAt'];

    public function __construct(protected string $token, protected ?DateTimeImmutable $expiresAt, protected ?VOConstructionContext $context = null)
    {
        parent::__construct();
    }

    // Optional helper: generate a new token (domain-specific)
    public static function generate(int $ttlSeconds = 2592000): self
    {
        $token = bin2hex(random_bytes(32)); // 64 hex chars
        $expiresAt = new DateTimeImmutable(sprintf('+%d seconds', $ttlSeconds));

        return new self($token, $expiresAt);
    }

    // Optional helper: check if expired
    public function isExpired(): bool
    {
        return $this->expiresAt <= new DateTimeImmutable;
    }

    public function equals(VO $other): bool
    {
        return $other instanceof self
            && $this->token === $other->token
            && $this->expiresAt == $other->expiresAt;
    }
}
