<?php

namespace Zolta\Domain\ValueObjects;

use Zolta\Domain\Interfaces\VO;

final class Pagination extends ValueObject
{
    protected array $getters = ['items', 'total', 'perPage', 'currentPage', 'lastPage'];

    /** @var list<mixed> */
    protected array $items;

    protected int $total;

    protected int $perPage;

    protected int $currentPage;

    protected int $lastPage;

    /**
     * @param  list<mixed>  $items
     */
    public function __construct(
        array $items,
        int $total,
        int $perPage,
        int $currentPage,
        int $lastPage,
        ?VOConstructionContext $context = null
    ) {
        $resolved = self::resolveInternal([
            'items' => $items,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $currentPage,
            'lastPage' => $lastPage,
        ], $context);

        $this->items = $resolved['items'];
        $this->total = $resolved['total'];
        $this->perPage = $resolved['perPage'];
        $this->currentPage = $resolved['currentPage'];
        $this->lastPage = $resolved['lastPage'];
    }

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
