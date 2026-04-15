<?php

declare(strict_types=1);

namespace Zolta\Tests\Unit\Domain\ValueObjects;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Zolta\Domain\ValueObjects\AccessToken;

final class AccessTokenTest extends TestCase
{
    public function test_generate_produces_64_hex_char_token(): void
    {
        $token = AccessToken::generate();

        $this->assertSame(64, strlen($token->get('token')));
        $this->assertTrue(ctype_xdigit($token->get('token')));
    }

    public function test_generate_produces_unique_tokens(): void
    {
        $token1 = AccessToken::generate();
        $token2 = AccessToken::generate();

        $this->assertNotSame($token1->get('token'), $token2->get('token'));
    }

    public function test_generate_sets_expiration(): void
    {
        $token = AccessToken::generate(3600);

        $expiresAt = $token->get('expiresAt');
        $this->assertNotNull($expiresAt);
    }

    public function test_is_expired_for_past_date(): void
    {
        $token = new AccessToken('abc123', new DateTimeImmutable('-1 day'));

        $this->assertTrue($token->isExpired());
    }

    public function test_is_not_expired_for_future_date(): void
    {
        $token = new AccessToken('abc123', new DateTimeImmutable('+1 day'));

        $this->assertFalse($token->isExpired());
    }

    public function test_equality(): void
    {
        $date = new DateTimeImmutable('2025-01-01');
        $token1 = new AccessToken('same_token', $date);
        $token2 = new AccessToken('same_token', $date);

        $this->assertTrue($token1->equals($token2));
    }

    public function test_inequality_different_token(): void
    {
        $date = new DateTimeImmutable('2025-01-01');
        $token1 = new AccessToken('token_a', $date);
        $token2 = new AccessToken('token_b', $date);

        $this->assertFalse($token1->equals($token2));
    }

    public function test_to_array_contains_token_and_expires_at(): void
    {
        $date = new DateTimeImmutable('2025-06-15T10:00:00+00:00');
        $token = new AccessToken('mytoken', $date);

        $arr = $token->toArray();

        $this->assertArrayHasKey('token', $arr);
        $this->assertArrayHasKey('expiresAt', $arr);
        $this->assertSame('mytoken', $arr['token']);
    }

    public function test_null_expires_at(): void
    {
        $token = new AccessToken('noexpiry', null);

        $this->assertNull($token->get('expiresAt'));
    }
}
