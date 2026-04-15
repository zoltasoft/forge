<?php

declare(strict_types=1);

namespace Zolta\Tests\Unit\Domain\ValueObjects;

use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Zolta\Domain\Policies\PasswordPolicy;
use Zolta\Domain\Rules\NonEmptyRule;
use Zolta\Domain\Rules\PasswordPolicyRule;

final class PasswordPolicyTest extends TestCase
{
    // ── PasswordPolicy (hashing service) ────────────────────────────────

    public function test_apply_hashes_valid_password(): void
    {
        $policy = new PasswordPolicy(8);

        $hash = $policy->apply('SecurePass1!');

        $this->assertNotSame('SecurePass1!', $hash);
        $this->assertTrue(password_verify('SecurePass1!', $hash));
    }

    public function test_apply_rejects_empty_password(): void
    {
        $policy = new PasswordPolicy;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('empty');

        $policy->apply('');
    }

    public function test_apply_rejects_short_password(): void
    {
        $policy = new PasswordPolicy(10);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('at least 10');

        $policy->apply('short');
    }

    public function test_apply_accepts_exact_min_length(): void
    {
        $policy = new PasswordPolicy(8);

        $hash = $policy->apply('12345678');

        $this->assertTrue(password_verify('12345678', $hash));
    }

    public function test_verify_correct_password(): void
    {
        $policy = new PasswordPolicy;
        $hash = $policy->apply('CorrectHorse');

        $this->assertTrue($policy->verify('CorrectHorse', $hash));
    }

    public function test_verify_wrong_password(): void
    {
        $policy = new PasswordPolicy;
        $hash = $policy->apply('CorrectHorse');

        $this->assertFalse($policy->verify('WrongHorse', $hash));
    }

    public function test_bcrypt_hash_format(): void
    {
        $policy = new PasswordPolicy;
        $hash = $policy->apply('ValidPassword');

        // BCrypt hashes start with $2y$
        $this->assertStringStartsWith('$2y$', $hash);
    }

    // ── PasswordPolicyRule (validation rule) ────────────────────────────

    public function test_rule_passes_for_valid_password(): void
    {
        $rule = new PasswordPolicyRule;

        // Should not throw
        $rule->validate('ValidPass1');
        $this->addToAssertionCount(1);
    }

    public function test_rule_fails_for_short_password(): void
    {
        $rule = new PasswordPolicyRule;

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('at least 8');

        $rule->validate('short');
    }

    public function test_rule_respects_custom_min_length(): void
    {
        $rule = new PasswordPolicyRule(['minLength' => 12]);

        $this->expectException(DomainException::class);

        $rule->validate('ShortPass1');
    }

    public function test_rule_min_length_overridden_via_options(): void
    {
        $rule = new PasswordPolicyRule(['minLength' => 5]);

        // Should not throw
        $rule->validate('12345');
        $this->addToAssertionCount(1);
    }

    public function test_rule_rejects_non_string(): void
    {
        $rule = new PasswordPolicyRule;

        $this->expectException(DomainException::class);

        $rule->validate(12345);
    }

    // ── NonEmptyRule ────────────────────────────────────────────────────

    public function test_non_empty_rule_passes_for_non_empty_string(): void
    {
        $rule = new NonEmptyRule;

        $rule->validate('hello');
        $this->addToAssertionCount(1);
    }

    public function test_non_empty_rule_fails_for_null(): void
    {
        $rule = new NonEmptyRule;

        $this->expectException(InvalidArgumentException::class);
        $rule->validate(null);
    }

    public function test_non_empty_rule_fails_for_empty_string(): void
    {
        $rule = new NonEmptyRule;

        $this->expectException(InvalidArgumentException::class);
        $rule->validate('');
    }

    public function test_non_empty_rule_fails_for_whitespace_only(): void
    {
        $rule = new NonEmptyRule;

        $this->expectException(InvalidArgumentException::class);
        $rule->validate('   ');
    }

    public function test_non_empty_rule_custom_param_name(): void
    {
        $rule = new NonEmptyRule;

        try {
            $rule->validate('', ['paramName' => 'email']);
            $this->fail('Expected InvalidArgumentException');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('email', $e->getMessage());
        }
    }
}
