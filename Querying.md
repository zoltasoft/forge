# 🔍 Zolta Forge — Unified Querying & Repository Options

### **Domain → Application → Infrastructure Query Flow Explained**

### With Complete Usage Examples for Developers

---

# Overview

This document explains how **Query Options** flow through the Zolta Forge stack:

```
HTTP Request → DTO → Query → QueryHandler → QueryOptionsFactory
→ AbstractQueryOptions → RepositoryQuery
→ EloquentBaseRepository → Eloquent Builder → Database
```

It also shows how `repositoryQuery($options)` normalizes domain-level query inputs into a **canonical infrastructure query model** (`RepositoryQuery`) used consistently across your Eloquent repositories.

Developers can use filtering, sorting, includes, pagination, and even streaming in a **simple, framework-agnostic way**.

---

# 1. The Core Idea Behind Query Normalization

Your infrastructure repository must support **multiple input types**:

- Domain `AbstractQueryOptions`
- Application arrays from DTOs
- Query objects from CQRS
- API request query params
- Raw arrays from internal code
- Already-normalized `RepositoryQuery`

To make this uniform, every repository uses:

```php
$query = $this->repositoryQuery($options);
```

This method converts **any input** into a standardized `RepositoryQuery`.

---

# 2. How `repositoryQuery()` Works

From `EloquentBaseRepository`:

```php
protected function repositoryQuery(AbstractQueryOptions|RepositoryQuery|array|null $source = null): RepositoryQuery
{
    if ($source instanceof RepositoryQuery) {
        return $source;
    }

    return RepositoryQuery::fromOptions($source);
}
```

### ✔ Accepts:

- Domain `AbstractQueryOptions`
- Already-built `RepositoryQuery`
- Raw arrays
- Null

### ✔ Always returns:

- A unified **RepositoryQuery** object ready for Infrastructure adapters

### ✔ Guarantees:

- Every repository method (`all()`, `paginate()`, `count()`, `cursor()`) receives consistent input
- Always safe to apply filters, sorting, includes, etc.
- Infra layer never needs to guess the query format

---

# 3. End-to-End Use Case: Listing Users

We will track **one request** from HTTP → Domain → Infra → DB → Domain output.

---

## 3.1 HTTP Layer: Request Validation & Extraction

Example request:

```
GET /users?filter[email]=john@example.com&include[]=role&sort=-created_at
```

**ListUsersRequest:**

```php
public function options(): array
{
    return [
        'default_include' => ['role', 'permissions', 'socialAccounts', 'socialAccounts.provider'],
        'strict'          => true,
        'allowed_filters' => ['email', 'username'],
        'allowed_sorts'   => ['email', 'username', 'created_at'],
    ];
}
```

**Extracted query params:**

```php
public function queryParams(): array
{
    return [
        'filter' => ['type' => 'array'],
        'include' => ['type' => 'array'],
        'sort' => ['type' => 'array'],
        'page' => ['type' => 'integer'],
        'per_page' => ['type' => 'integer'],
    ];
}
```

---

## 3.2 Application Layer: DTO → Query → Handler

### DTO:

```php
final class ListUsersDTO extends InputDTO
{
    public function __construct(public readonly array $options = []) {}
}
```

### Query Object:

```php
final class ListUsersQuery extends Query
{
    public function __construct(public readonly array $options = []) {}
}
```

### Query Handler:

```php
public function __invoke(ListUsersQuery $query, QueryOptionsFactory $optionsFactory): Option
{
    $options = $optionsFactory->make($query->options);

    $users = $this->userRepository->getAllUsers($options);

    return Option::some(new UserCollectionPayload($users));
}
```

### 🔥 Key point:

`$optionsFactory->make()` → produces **AbstractQueryOptions** (Domain object)

---

## 3.3 Infrastructure: Normalizing Into `RepositoryQuery`

Inside the repository:

```php
$query = $this->repositoryQuery($options);
```

This ensures all inputs become a `RepositoryQuery` containing:

```php
filters
include
sort
limit
page
context
fields
```

Infra always receives consistent data.

---

## 3.4 Eloquent Query Building

From EloquentBaseRepository:

```
repositoryQuery → buildQuery()
→ applyFilters()
→ applySorting()
→ applyIncludes()
→ applyLimit()
```

Produces a fully valid Eloquent query builder.

---

## 3.5 Mapping Results Back to Domain

```php
return $this->all($query)
    ->map(fn(Model $model) => UserMapper::toDomain($model))
    ->all();
```

Domain remains persistence-agnostic.

---

# 4. Streaming Large Result Sets

Developer can request streaming:

```
GET /users?context[stream]=true
```

Repository detects:

```php
if (!empty($query->context()['stream'])) {
    foreach ($this->cursor($query) as $model) {
        ...
    }
}
```

### Benefits:

✔ Low memory usage  
✔ Ideal for large exports  
✔ Zero additional logic needed

---

# 5. Common Query Examples

### 5.1 Basic Filtering

```php
$query = RepositoryQuery::fromOptions([
    'filters' => ['email' => 'john@example.com'],
]);
```

---

### 5.2 Relation Filtering

```php
$query = RepositoryQuery::fromOptions([
    'filters' => ['role.name' => 'admin'],
    'include' => ['role', 'permissions'],
]);
```

---

### 5.3 Sorting

```php
'sort' => ['-created_at', 'username']
```

---

### 5.4 Pagination

```php
'page' => 2,
'limit' => 25
```

---

### 5.5 Custom Filter Objects

```php
$filters['search'] = new SearchFilter('john', ['name', 'email']);
```

---

### 5.6 Streaming Mode

```php
'context' => ['stream' => true]
```

---

# 6. Why Developers Should Use This System

### ✔ Clean separation between layers

### ✔ Domain remains persistence-agnostic

### ✔ Infra gets consistent data

### ✔ Supports advanced features:

- Relation filtering
- Nested includes
- Streaming
- Custom filters
- Strict mode for security

### ✔ Reduces boilerplate dramatically

### ✔ Makes controllers and handlers extremely clean

---

# 7. Developer Quick Start

### Step 1 — Accept the request

```php
public function index(ListUsersRequest $request, ListUsersDTO $dto)
{
    return $this->queryBus->dispatch(
        new ListUsersQuery($dto->options)
    );
}
```

---

### Step 2 — Let QueryHandler convert options

```php
$options = $optionsFactory->make($query->options);
```

---

### Step 3 — Repository handles everything

```php
$users = $this->userRepository->getAllUsers($options);
```

---

# 8. Summary Diagram

```
HTTP → Request → DTO
    → Query → QueryHandler
        → QueryOptionsFactory
            → AbstractQueryOptions
                → repositoryQuery()
                    → RepositoryQuery
                        → Eloquent Query Builder
                            → DB
                                → UserMapper → Domain
```

---

# Final Notes

This querying mechanism is extremely powerful:

- fully DDD-compliant
- reusable across services
- strict, secure, and composable
- supports large datasets, nested relations, and custom filters

---
