<?php

namespace Zolta\Domain\ValueObjects;

use InvalidArgumentException;
use Zolta\Domain\Interfaces\VO;

/**
 * OAuthProvider enum implementing VO.
 */
enum OAuthProvider: string implements VO
{
    case Google = 'google';
    case Microsoft = 'microsoft';
    case Github = 'github';

    /**
     * Resolve from array context.
     * Accepts keys: provider, oauth_provider, provider_name
     * If missing, defaults to Google.
     *
     * @param  array<string,mixed>|null  $data
     */
    public static function resolve(
        ?array $data = null,
        ?VOConstructionContext $context = null
    ): static {
        if ($data === null) {
            return self::Google;
        }

        $val = $data['provider'] ?? $data['oauth_provider'] ?? $data['provider_name'] ?? null;
        if ($val === null) {
            return self::Google;
        }

        return self::fromString((string) $val);
    }

    public static function fromString(string $value): static
    {
        $s = strtolower(trim($value));

        return match ($s) {
            'google' => self::Google,
            'microsoft' => self::Microsoft,
            'github' => self::Github,
            default => throw new InvalidArgumentException("Invalid OAuthProvider value: {$value}"),
        };
    }

    public static function default(): static
    {
        return self::Google;
    }

    public function get(): string
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return ['provider' => $this->value];
    }

    public function equals(VO $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }
}
