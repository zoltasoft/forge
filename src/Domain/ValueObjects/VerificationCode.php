<?php

namespace Zolta\Domain\ValueObjects;

use Zolta\Domain\Interfaces\VO;
use Zolta\Domain\Rules\RegexRule;

final class VerificationCode extends ValueObject
{
    protected array $getters = ['code'];

    private const PATTERN = '/^\d{6}$/';

    protected string $code;

    public function __construct(string $code, ?VOConstructionContext $context = null)
    {
        // Validate using RegexRule
        (new RegexRule(self::PATTERN, 'verification_code'))->validate($code);

        $resolved = self::resolveInternal(['code' => $code], $context);
        $this->code = $resolved['code'];
    }

    /**
     * Generate a random 6-digit code
     */
    public static function generate(): self
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return new self($code);
    }

    /**
     * Compare VOs
     */
    public function equals(VO $other): bool
    {
        return $other instanceof self && $this->code === $other->code;
    }
}
