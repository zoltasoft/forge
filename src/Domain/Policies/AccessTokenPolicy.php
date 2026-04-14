<?php

declare(strict_types=1);

namespace Zolta\Domain\Policies;

use DateTimeImmutable;

final readonly class AccessTokenPolicy
{
    public function __construct(private int $ttl = 3600) {}

    /**
     * generate token and expiry. returns ['token' => string, 'expiresAt' => DateTimeImmutable]
     *
     * @return array{token: string, expiresAt: DateTimeImmutable}
     */
    public function generate(): array
    {
        $token = bin2hex(random_bytes(16));
        $expires = (new DateTimeImmutable)->modify('+'.$this->ttl.' seconds');

        return ['token' => $token, 'expiresAt' => $expires];
    }

    public function isValid(string $token, \DateTimeInterface $expiresAt): bool
    {
        return $expiresAt > new DateTimeImmutable;
    }
}
