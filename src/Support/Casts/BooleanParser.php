<?php

declare(strict_types=1);

namespace Zolta\Support\Casts;

final class BooleanParser
{
    /**
     * Parse various inputs into boolean or null if ambiguous.
     *
     * Returns:
     *  - true  => accepted truthy
     *  - false => explicitly falsy
     *  - null  => unknown / cannot decide (useful to treat as missing)
     */
    public static function parse(mixed $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }

        // handle numeric types
        if (is_int($value) || is_float($value)) {
            return $value == 1 ? true : ($value == 0 ? false : null);
        }

        // everything else -> use string canonicalization
        $str = strtolower(trim((string) $value));

        // explicit truthy tokens
        $truthy = ['1', 'true', 'on', 'yes'];
        // explicit falsy tokens
        $falsy = ['0', 'false', 'off', 'no', 'null', ''];

        if (in_array($str, $truthy, true)) {
            return true;
        }

        if (in_array($str, $falsy, true)) {
            return false;
        }

        // if you prefer, you can use filter_var as fallback:
        // $fv = filter_var($str, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        // if ($fv !== null) return $fv;

        // ambiguous value (e.g. 'maybe' or unexpected payload)
        return null;
    }
}
