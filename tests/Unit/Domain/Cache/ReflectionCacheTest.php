<?php

declare(strict_types=1);

namespace Zolta\Tests\Unit\Domain\Cache;

use Attribute;
use PHPUnit\Framework\TestCase;
use Zolta\Domain\Cache\ReflectionCache;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::TARGET_PARAMETER)]
class FakeAttr
{
    public function __construct(public string $value) {}
}

#[FakeAttr('class-level')]
class FakeTarget
{
    public function __construct(
        #[FakeAttr('param-foo')] public string $foo,
        #[FakeAttr('param-bar')] public int $bar = 1
    ) {}

    #[FakeAttr('method-level')]
    public function doWork(): void {}
}

final class ReflectionCacheTest extends TestCase
{
    protected function setUp(): void
    {
        ReflectionCache::clearRuntimeCache();
    }

    public function test_class_and_method_attributes_are_cached(): void
    {
        $classAttrs = ReflectionCache::getClassAttributes(FakeTarget::class);
        $this->assertNotEmpty($classAttrs);
        $this->assertSame(FakeAttr::class, $classAttrs[0]['class']);
        $this->assertSame(['class-level'], array_values($classAttrs[0]['arguments']));

        $methodAttrs = ReflectionCache::getMethodAttributes(FakeTarget::class, 'doWork');
        $this->assertNotEmpty($methodAttrs);
        $this->assertSame(FakeAttr::class, $methodAttrs[0]['class']);
        $this->assertSame(['method-level'], array_values($methodAttrs[0]['arguments']));
    }

    public function test_constructor_params_are_cached_and_cleared(): void
    {
        $params = ReflectionCache::getConstructorParams(FakeTarget::class);
        $this->assertCount(2, $params);
        $this->assertSame('foo', $params[0]['name']);
        $this->assertSame('bar', $params[1]['name']);
        $this->assertFalse($params[0]['allowsNull']);

        ReflectionCache::clear(FakeTarget::class);

        $paramsAfterClear = ReflectionCache::getConstructorParams(FakeTarget::class);
        $this->assertCount(2, $paramsAfterClear);
    }
}
