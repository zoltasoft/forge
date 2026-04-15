<?php

declare(strict_types=1);

namespace Zolta\Tests\Unit\Domain\Specifications;

use PHPUnit\Framework\TestCase;
use Zolta\Domain\Rules\MaxLengthRule;
use Zolta\Domain\Specifications\AllowedDomainSpecification;

final class SpecificationsAndRulesTest extends TestCase
{
    // ── AllowedDomainSpecification ──────────────────────────────────────

    public function test_allowed_domain_passes_for_correct_domain(): void
    {
        $spec = new AllowedDomainSpecification;

        $this->assertTrue(
            $spec->isSatisfiedBy('alice@gmail.com', ['allowed' => ['gmail.com', 'protonmail.com']])
        );
    }

    public function test_allowed_domain_fails_for_wrong_domain(): void
    {
        $spec = new AllowedDomainSpecification;

        $this->assertFalse(
            $spec->isSatisfiedBy('alice@evil.com', ['allowed' => ['gmail.com']])
        );
    }

    public function test_allowed_domain_passes_when_no_allowed_list(): void
    {
        $spec = new AllowedDomainSpecification;

        $this->assertTrue(
            $spec->isSatisfiedBy('any@any.com')
        );
    }

    public function test_allowed_domain_fails_for_address_without_at(): void
    {
        $spec = new AllowedDomainSpecification;

        $this->assertFalse(
            $spec->isSatisfiedBy('noatsign', ['allowed' => ['gmail.com']])
        );
    }

    public function test_allowed_domain_case_sensitive(): void
    {
        $spec = new AllowedDomainSpecification;

        // Domain comparison is case-sensitive in current implementation
        $this->assertFalse(
            $spec->isSatisfiedBy('alice@Gmail.COM', ['allowed' => ['gmail.com']])
        );
    }

    public function test_allowed_domain_message(): void
    {
        $spec = new AllowedDomainSpecification;

        $this->assertNotEmpty($spec->message());
    }

    // ── MaxLengthRule ───────────────────────────────────────────────────

    public function test_max_length_passes_for_short_string(): void
    {
        $rule = new MaxLengthRule;

        $rule->validate('short', ['max' => 10]);
        $this->addToAssertionCount(1);
    }

    public function test_max_length_fails_for_long_string(): void
    {
        $rule = new MaxLengthRule;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('cannot exceed 5');

        $rule->validate('toolongstring', ['max' => 5]);
    }

    public function test_max_length_passes_for_exact_length(): void
    {
        $rule = new MaxLengthRule;

        $rule->validate('12345', ['max' => 5]);
        $this->addToAssertionCount(1);
    }

    public function test_max_length_throws_without_max_option(): void
    {
        $rule = new MaxLengthRule;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("requires option 'max'");

        $rule->validate('anything');
    }

    public function test_max_length_handles_multibyte(): void
    {
        $rule = new MaxLengthRule;

        // 3 multibyte characters
        $rule->validate('日本語', ['max' => 3]);
        $this->addToAssertionCount(1);
    }

    public function test_max_length_fails_multibyte_over_limit(): void
    {
        $rule = new MaxLengthRule;

        $this->expectException(\InvalidArgumentException::class);

        $rule->validate('日本語テスト', ['max' => 3]);
    }
}
