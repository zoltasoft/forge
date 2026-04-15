<?php

declare(strict_types=1);

namespace Doctrine\ORM;

interface EntityManagerInterface
{
    public function createQueryBuilder(): QueryBuilder;
}

class QueryBuilder
{
    public function from(string $from, ?string $alias = null): self
    {
        return $this;
    }

    public function where(string $pred): self
    {
        return $this;
    }

    public function leftJoin(string $fromAlias, string $alias, ?string $conditionType = null, ?string $condition = null): self
    {
        return $this;
    }

    public function addSelect(?string $select = null): self
    {
        return $this;
    }

    public function andWhere(string $pred): self
    {
        return $this;
    }

    public function setParameter(string|int $key, mixed $value): self
    {
        return $this;
    }

    public function expr(): Query\Expr
    {
        return new Query\Expr;
    }

    public function addOrderBy(string $sort, ?string $order = null): self
    {
        return $this;
    }

    public function select(string|array|null $select = null): self
    {
        return $this;
    }

    public function setMaxResults(?int $maxResults): self
    {
        return $this;
    }

    public function getQuery(): Query
    {
        return new Query;
    }
}

class Query
{
    public function getResult(): array
    {
        return [];
    }

    public function getOneOrNullResult(): mixed
    {
        return null;
    }
}

namespace Doctrine\ORM\Tools\Pagination;

class Paginator implements \Countable, \IteratorAggregate
{
    public function __construct(object $query) {}

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator([]);
    }

    public function count(): int
    {
        return 0;
    }
}

namespace Doctrine\ORM\Query;

class Expr
{
    public function like(string $x, string $y): string
    {
        return '';
    }

    public function orX(string ...$expressions): string
    {
        return '';
    }
}

namespace Doctrine\ORM\Query\Expr;

class Join
{
    public const WITH = 'WITH';
}

namespace Symfony\Component\Messenger;

interface MessageBusInterface
{
    public function dispatch(object $message): mixed;
}
