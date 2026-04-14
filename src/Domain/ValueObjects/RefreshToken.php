<?php

namespace Zolta\Domain\ValueObjects;

use DateTimeImmutable;
use InvalidArgumentException;
use Zolta\Domain\Interfaces\VO;

final class RefreshToken extends ValueObject
{
    protected array $getters = ['token', 'expiresAt'];

    protected string $token;

    protected DateTimeImmutable $expiresAt;

    public function __construct(string $token, DateTimeImmutable $expiresAt, ?VOConstructionContext $context = null)
    {
        $token = trim($token);
        if ($token === '') {
            throw new InvalidArgumentException('Refresh token cannot be empty.');
        }

        $resolved = self::resolveInternal([
            'token' => $token,
            'expiresAt' => $expiresAt,
        ], $context);

        $this->token = $resolved['token'];
        $this->expiresAt = $resolved['expiresAt'];
    }

    public function equals(VO $other): bool
    {
        return $other instanceof self
            && $this->token === $other->token
            && $this->expiresAt == $other->expiresAt; // allow DateTime loose equality
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
}
