<?php

declare(strict_types=1);

namespace Zolta\Tests\Unit\Domain\Transformers;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Zolta\Domain\Transformers\DateTimeNormalizer;
use Zolta\Domain\Transformers\EmailNormalizer;

final class TransformersTest extends TestCase
{
    // ── EmailNormalizer ─────────────────────────────────────────────────

    public function test_email_normalizer_trims_and_lowercases(): void
    {
        $normalizer = new EmailNormalizer;

        $result = $normalizer->transform('  Alice@Example.COM  ');

        $this->assertSame('alice@example.com', $result);
    }

    public function test_email_normalizer_with_trim_disabled(): void
    {
        $normalizer = new EmailNormalizer;

        $result = $normalizer->transform('  test@test.com  ', ['trim' => false, 'lowercase' => true]);

        $this->assertSame('  test@test.com  ', $result);
    }

    public function test_email_normalizer_with_lowercase_disabled(): void
    {
        $normalizer = new EmailNormalizer;

        $result = $normalizer->transform('Alice@Test.COM', ['lowercase' => false]);

        $this->assertSame('Alice@Test.COM', $result);
    }

    public function test_email_normalizer_null_returns_empty_string(): void
    {
        $normalizer = new EmailNormalizer;

        $result = $normalizer->transform(null);

        $this->assertSame('', $result);
    }

    // ── DateTimeNormalizer ──────────────────────────────────────────────

    public function test_datetime_normalizer_null_returns_null(): void
    {
        $normalizer = new DateTimeNormalizer;

        $result = $normalizer->transform(null);

        $this->assertNull($result);
    }

    public function test_datetime_normalizer_from_datetime_interface(): void
    {
        $normalizer = new DateTimeNormalizer;
        $input = new \DateTime('2025-01-15 10:00:00');

        $result = $normalizer->transform($input);

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertSame('2025-01-15', $result->format('Y-m-d'));
    }

    public function test_datetime_normalizer_from_string(): void
    {
        $normalizer = new DateTimeNormalizer;

        $result = $normalizer->transform('2025-06-01');

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertSame('2025-06-01', $result->format('Y-m-d'));
    }

    public function test_datetime_normalizer_with_format_option(): void
    {
        $normalizer = new DateTimeNormalizer;

        $result = $normalizer->transform('15/06/2025', ['format' => 'd/m/Y']);

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertSame('2025-06-15', $result->format('Y-m-d'));
    }

    public function test_datetime_normalizer_invalid_format_throws(): void
    {
        $normalizer = new DateTimeNormalizer;

        $this->expectException(InvalidArgumentException::class);

        $normalizer->transform('not-a-date', ['format' => 'Y-m-d']);
    }

    public function test_datetime_normalizer_preserves_immutable(): void
    {
        $normalizer = new DateTimeNormalizer;
        $input = new DateTimeImmutable('2025-03-01');

        $result = $normalizer->transform($input);

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
    }
}
