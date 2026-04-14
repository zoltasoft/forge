<?php

declare(strict_types=1);

namespace Zolta\Domain\Transformers;

use Zolta\Domain\Contracts\Transformer;

final class EmailNormalizer extends Transformer
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function transform(mixed $value, array $options = []): string
    {

        if ($value === null) {
            return '';
        }
        $s = (string) $value;
        if (($options['trim'] ?? true) === true) {
            $s = trim($s);
        }
        if (($options['lowercase'] ?? true) === true) {
            $s = mb_strtolower($s);
        }

        return $s;
    }
}
