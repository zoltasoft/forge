<?php

declare(strict_types=1);

namespace Zolta\Domain\Repositories\Query;

/**
 * Domain-level Query Options (framework agnostic).
 * Concrete implementations live in infrastructure
 */
abstract class AbstractQueryOptions
{
    protected ?int $limit = null;

    protected ?int $page = null;

    /** @var array<string, mixed> */
    protected array $filters = [];

    /** @var list<string> */
    protected array $include = [];

    /** @var list<string> */
    protected array $sort = [];

    /** @var array<string, mixed> */
    protected array $context = [];

    /**
     * @param array{
     *     filters?: array<string, mixed>,
     *     include?: array<int, string>,
     *     sort?: array<int, string>,
     *     limit?: int|null,
     *     page?: int|null,
     *     context?: array<string, mixed>
     * } $payload
     */
    public function __construct(array $payload = [])
    {
        $this->filters = $payload['filters'] ?? [];
        $this->include = $payload['include'] ?? [];
        $this->sort = $payload['sort'] ?? [];
        $this->limit = $payload['limit'] ?? null;
        $this->page = $payload['page'] ?? null;
        $this->context = $payload['context'] ?? [];
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    /**
     * @return array<string, mixed>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return list<string>
     */
    public function getInclude(): array
    {
        return $this->include;
    }

    /**
     * @return list<string>
     */
    public function getSort(): array
    {
        return $this->sort;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function withContext(array $context): static
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

    /**
     * Convert to a simple array representation consumable by infra.
     * Implementations should return a stable array with keys:
     *  - filters, include, sort, limit, page, context
     *
     * @return array{
     *     filters: array<string, mixed>,
     *     include: list<string>,
     *     sort: list<string>,
     *     limit: int|null,
     *     page: int|null,
     *     context: array<string, mixed>
     * }
     */
    abstract public function toArray(): array;
}
