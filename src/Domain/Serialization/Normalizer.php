<?php

declare(strict_types=1);

namespace Zolta\Domain\Serialization;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Zolta\Domain\Serialization\Contracts\NormalizerInterface;

class Normalizer implements NormalizerInterface
{
    private readonly Serializer $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer([new ObjectNormalizer]);
    }

    /**
     * @return array<string, mixed>
     */
    public function normalize(object $object): array
    {
        return $this->serializer->normalize($object);
    }
}
