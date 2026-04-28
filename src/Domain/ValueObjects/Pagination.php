<?php

declare(strict_types=1);

namespace Zolta\Domain\ValueObjects;

use Zolta\Domain\Interfaces\VO;

final class Pagination extends ValueObject
{
    protected array $getters = ['items', 'total', 'perPage', 'currentPage', 'lastPage'];

    /**
     * @param  list<mixed>  $items
     */
    public function __construct(
        /** @var list<mixed> */
        public readonly array $items,
        public readonly int $total,
        public readonly int $perPage,
        public readonly int $currentPage,
        public readonly int $lastPage,
    ) {}

    public function equals(VO $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->items === $other->items
            && $this->total === $other->total
            && $this->perPage === $other->perPage
            && $this->currentPage === $other->currentPage
            && $this->lastPage === $other->lastPage;
    }
}
