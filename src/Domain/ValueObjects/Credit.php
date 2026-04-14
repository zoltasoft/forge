<?php

namespace Zolta\Domain\ValueObjects;

use Zolta\Domain\Interfaces\VO;
use Zolta\Domain\Invariants\CreditInvariant;

final class Credit extends ValueObject
{
    protected array $getters = ['amount', 'currency'];

    protected float $amount;

    protected string $currency;

    public function __construct(float $amount, string $currency, ?VOConstructionContext $context = null)
    {
        (new CreditInvariant)->ensure(['amount' => $amount, 'currency' => $currency]);

        $resolved = self::resolveInternal([
            'amount' => $amount,
            'currency' => strtoupper($currency),
        ], $context);

        $this->amount = $resolved['amount'];
        $this->currency = $resolved['currency'];
    }

    public function equals(VO $other): bool
    {
        return $other instanceof self
            && $this->amount === $other->amount
            && $this->currency === $other->currency;
    }
}
