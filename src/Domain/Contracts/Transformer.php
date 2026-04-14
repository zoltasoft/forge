<?php

declare(strict_types=1);

namespace Zolta\Domain\Contracts;

abstract class Transformer implements TransformerInterface
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function transform(mixed $value, array $options = []): mixed
    {
        throw new \BadMethodCallException('transform() not implemented');
    }

    /**
     * Compose (pipe) transformers: a->b
     */
    public function and(TransformerInterface $transformer): TransformerInterface
    {
        $a = $this;

        return new class($a, $transformer) extends Transformer
        {
            public function __construct(private readonly TransformerInterface $a, private readonly TransformerInterface $b) {}

            /**
             * @param  array<string, mixed>  $options
             */
            public function transform(mixed $value, array $options = []): mixed
            {
                $v = $this->a->transform($value, $options);

                return $this->b->transform($v, $options);
            }
        };
    }
}
