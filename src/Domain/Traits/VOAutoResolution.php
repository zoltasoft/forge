<?php

declare(strict_types=1);

namespace Zolta\Domain\Traits;

use DomainException;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use Zolta\Domain\ValueObjects\VOConstructionContext;

trait VOAutoResolution
{
    /**
     * Factory-style entry point — resolves and instantiates.
     *
     * @param  array<string, mixed>  $data
     */
    public static function resolve(array $data = [], ?VOConstructionContext $voConstructionContext = null): static
    {
        $resolved = static::resolveInternal($data, $voConstructionContext);

        return static::new($resolved);
    }

    /**
     * Decoupled “brain”: resolves and validates attributes, returns associative array.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected static function resolveInternal(array $data, ?VOConstructionContext $voConstructionContext = null): array
    {
        $voConstructionContext ??= new VOConstructionContext;
        $runtimePreprocessors = $voConstructionContext->runtimePreprocessors ?? [];
        $runtimeOptions = $voConstructionContext->runtimeOptions ?? [];

        $reflectionClass = new ReflectionClass(static::class);
        $placeholder = $reflectionClass->newInstanceWithoutConstructor();

        // --- Initialize uninitialized typed array properties (class + parents)
        $currentRef = $reflectionClass;
        while ($currentRef) {
            foreach ($currentRef->getProperties() as $p) {
                if (
                    $p->getType() instanceof \ReflectionNamedType &&
                    $p->getType()->getName() === 'array' &&
                    ! $p->isInitialized($placeholder)
                ) {
                    $p->setValue($placeholder, []);
                }
            }
            $currentRef = $currentRef->getParentClass();
        }

        // --- Normalize preprocessor keys (strip nested prefixes like "email.address")
        $normalizedRuntimePreprocessors = [];
        foreach ($runtimePreprocessors as $key => $fn) {
            $segments = explode('.', (string) $key);
            $normalizedRuntimePreprocessors[end($segments)] = $fn;
        }
        $runtimePreprocessors = $normalizedRuntimePreprocessors;

        // --- Step 0: Collect VO properties (only from $data keys)
        $voProperties = [];
        $incomingKeys = array_keys($data);
        foreach (
            $reflectionClass->getProperties(
                ReflectionProperty::IS_PRIVATE |
                    ReflectionProperty::IS_PROTECTED |
                    ReflectionProperty::IS_PUBLIC
            ) as $reflectionProperty
        ) {
            $name = $reflectionProperty->getName();
            // Skip internals and props not in $data
            if (
                ! in_array($name, $incomingKeys, true) ||
                in_array($name, ['getters', 'context'], true)
            ) {
                continue;
            }
            $voProperties[] = $name;
        }

        // --- Step 1: Validate runtime preprocessors (after normalization)
        foreach (array_keys($runtimePreprocessors) as $propName) {
            if (! in_array($propName, $voProperties, true)) {
                throw new InvalidArgumentException(
                    "Runtime preprocessor cannot be applied: property '{$propName}' does not exist in ".static::class
                );
            }
        }

        // --- Step 2: Merge preprocessors (runtime overrides default)
        $voDefaults = [];
        if (method_exists($placeholder, 'defaultPreprocessors')) {
            try {
                $voDefaults = $placeholder->defaultPreprocessors() ?: [];
            } catch (\Throwable) {
                $voDefaults = [];
            }
        }
        $preprocessors = array_merge($voDefaults, $runtimePreprocessors);

        // Helper: short class name
        $classBase = static function (string $fqcn): string {
            $pos = strrpos($fqcn, '\\');

            return $pos === false ? $fqcn : substr($fqcn, $pos + 1);
        };

        // Helper: find the runtime options bucket for this property, supporting keys like "email.address"
        /**
         * @param  array<string, mixed>  $runtimeOptions
         */
        $bucketForProp = static function (array $runtimeOptions, string $propName): array {
            if (isset($runtimeOptions[$propName]) && is_array($runtimeOptions[$propName])) {
                return $runtimeOptions[$propName];
            }
            // fallback: try any key whose last segment matches $propName
            foreach ($runtimeOptions as $k => $v) {
                if (! is_array($v)) {
                    continue;
                }
                $segments = explode('.', (string) $k);
                if (end($segments) === $propName) {
                    return $v;
                }
            }

            return [];
        };

        // --- Step 3: Process properties
        $values = [];
        foreach ($voProperties as $voProperty) {
            $value = $data[$voProperty] ?? null;

            // 3.1 Apply preprocessor if exists
            if (isset($preprocessors[$voProperty]) && is_callable($preprocessors[$voProperty])) {
                $value = $preprocessors[$voProperty]($value);
            }

            // 3.2 Apply Transform / Rule / Specification attributes
            $reflectionProperty = $reflectionClass->getProperty($voProperty);
            $propAttrs = $reflectionProperty->getAttributes();
            $propRuntimeBucket = $bucketForProp($runtimeOptions, $voProperty);

            // First: TRANSFORMS (order matters — transform before validation)
            foreach ($propAttrs as $attr) {
                if ($attr->getName() !== \Zolta\Domain\Attributes\Transform::class) {
                    continue;
                }

                $args = $attr->getArguments();
                $transformerClass = $args[0] ?? null;
                if (! $transformerClass) {
                    continue;
                }

                $transformer = new $transformerClass;
                if (! $transformer instanceof \Zolta\Domain\Contracts\TransformerInterface) {
                    throw new DomainException("Transformer {$transformerClass} must implement TransformerInterface");
                }

                $staticOptions = $args[1] ?? [];
                // allow both FQCN and short class name
                $runtimeTransOptions = $propRuntimeBucket[$transformerClass]
                    ?? $propRuntimeBucket[$classBase($transformerClass)]
                    ?? [];

                if (is_array($runtimeTransOptions) && is_array($staticOptions)) {
                    $options = array_replace_recursive($staticOptions, $runtimeTransOptions);
                } elseif (! empty($runtimeTransOptions)) {
                    $options = is_array($runtimeTransOptions) ? $runtimeTransOptions : ['value' => $runtimeTransOptions];
                } else {
                    $options = $staticOptions;
                }

                $value = $transformer->transform($value, $options);
            }

            // Second: RULES and SPECIFICATIONS (validate after transforms)
            foreach ($propAttrs as $propAttr) {
                $attrClass = $propAttr->getName();
                $args = $propAttr->getArguments();

                // Rules
                if ($attrClass === \Zolta\Domain\Attributes\UseRule::class) {
                    $ruleClass = $args[0] ?? null;
                    if ($ruleClass) {
                        $rule = new $ruleClass;
                        if (! $rule instanceof \Zolta\Domain\Contracts\RuleInterface) {
                            throw new DomainException("Rule {$ruleClass} must implement RuleInterface");
                        }

                        $staticOptions = $args[1] ?? [];

                        // support both FQCN and short class name
                        $runtimeRuleOptions = $propRuntimeBucket[$ruleClass]
                            ?? $propRuntimeBucket[$classBase($ruleClass)]
                            ?? [];

                        if (is_array($runtimeRuleOptions) && is_array($staticOptions)) {
                            $ruleOptions = array_replace_recursive($staticOptions, $runtimeRuleOptions);
                        } elseif (! empty($runtimeRuleOptions)) {
                            $ruleOptions = is_array($runtimeRuleOptions)
                                ? $runtimeRuleOptions
                                : ['value' => $runtimeRuleOptions];
                        } else {
                            $ruleOptions = $staticOptions;
                        }

                        $rule->validate($value, $ruleOptions);
                    }
                }

                // Specifications
                if ($attrClass === \Zolta\Domain\Attributes\UseSpecification::class) {
                    $specClass = $args[0] ?? null;
                    if ($specClass) {
                        $spec = new $specClass;
                        if (! $spec instanceof \Zolta\Domain\Contracts\SpecificationInterface) {
                            throw new DomainException("Specification {$specClass} must implement SpecificationInterface");
                        }

                        $staticSpecArgs = $args[1] ?? [];
                        $runtimeSpecOptions = $propRuntimeBucket[$specClass]
                            ?? $propRuntimeBucket[$classBase($specClass)]
                            ?? [];

                        $specOptions = is_array($runtimeSpecOptions)
                            ? array_replace_recursive($staticSpecArgs, $runtimeSpecOptions)
                            : $staticSpecArgs;

                        if (! $spec->isSatisfiedBy($value, $specOptions)) {
                            throw new DomainException("Specification {$specClass} not satisfied for {$voProperty}");
                        }
                    }
                }
            }

            $values[$voProperty] = $value;

            // --- Step 3.3: If value is a nested ValueObject, run its class-level policies/invariants
            $type = $reflectionProperty->getType();
            if ($type instanceof \ReflectionNamedType) {
                $typeName = $type->getName();

                // detect nested VO
                if (is_subclass_of($typeName, \Zolta\Domain\Interfaces\VO::class)) {
                    if (is_array($value)) {
                        // recursively resolve using the same runtime context
                        $value = $typeName::resolve($value, $voConstructionContext);
                    }

                    // once constructed, its class-level policy will apply inside its own resolveInternal()
                }
            }
        }

        // --- Step 4: Apply class-level invariants & policies ---
        $classAttrs = \Zolta\Domain\Cache\ReflectionCache::getClassAttributes(static::class);
        $voForClassChecks = null;

        foreach ($classAttrs as $classAttr) {
            $attrClass = $classAttr['class'] ?? null;
            $args = $classAttr['arguments'] ?? [];
            $options = $runtimeOptions['class'][$attrClass] ?? ($args[1] ?? []);

            if ($attrClass === \Zolta\Domain\Attributes\UseInvariant::class) {
                $invClass = $args[0] ?? null;
                if ($invClass) {
                    $inv = new $invClass;
                    $voForClassChecks ??= static::new($values);
                    $inv->ensure($voForClassChecks, $options);
                }
            }

            if ($attrClass === \Zolta\Domain\Attributes\UsePolicy::class) {
                $policyClass = $args[0] ?? null;
                if ($policyClass) {
                    $policy = new $policyClass;
                    $voForClassChecks ??= static::new($values);
                    $policy->apply($voForClassChecks, $options);
                }
            }
        }

        return $values;
    }

    /**
     * Instantiates the VO from resolved attributes.
     *
     * @param  array<string, mixed>  $resolved
     */
    protected static function new(array $resolved): static
    {
        $reflectionClass = new ReflectionClass(static::class);
        $instance = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($resolved as $name => $value) {
            if (! $reflectionClass->hasProperty($name)) {
                continue;
            }
            $prop = $reflectionClass->getProperty($name);
            $type = $prop->getType();

            // handle enum-typed properties
            if ($type instanceof \ReflectionNamedType) {
                $typeName = $type->getName();
                if (enum_exists($typeName)) {
                    if (is_object($value) && $value::class === $typeName) {
                        $prop->setValue($instance, $value);

                        continue;
                    }

                    $valueToUse = is_array($value)
                        ? ($value['value'] ?? reset($value))
                        : $value;

                    try {
                        if (method_exists($typeName, 'from')) {
                            $enumInstance = $typeName::from($valueToUse);
                        } elseif (method_exists($typeName, 'tryFrom')) {
                            $enumInstance = $typeName::tryFrom($valueToUse);

                            if ($enumInstance === null) {
                                throw new DomainException("Invalid enum value for {$typeName}");
                            }
                        } else {
                            throw new DomainException("Enum {$typeName} does not support scalar coercion");
                        }

                        $prop->setValue($instance, $enumInstance);

                        continue;
                    } catch (\Throwable $e) {
                        throw new DomainException("Failed to coerce enum property {$name}: ".$e->getMessage(), $e->getCode(), $e);
                    }
                }
            }

            // skip assigning null to non-nullable typed props
            if ($value === null && $type && ! $type->allowsNull()) {
                continue;
            }

            $prop->setValue($instance, $value);
        }

        // Ensure all typed nullable props are initialized
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if (! $reflectionProperty->isInitialized($instance)) {
                $type = $reflectionProperty->getType();
                if ($type && $type->allowsNull()) {
                    $reflectionProperty->setValue($instance, null);
                }
            }
        }

        return $instance;
    }

    /**
     * Safe getter utility.
     *
     * @param  array<int|string, callable|string>|string  $key
     */
    public function get(array|string $key): mixed
    {
        if (is_string($key)) {
            if (property_exists($this, $key)) {
                return $this->$key;
            }
            throw new InvalidArgumentException("Key {$key} is not accessible on ".static::class);
        }

        if (is_array($key)) {
            $result = [];
            foreach ($key as $k => $callbackOrNothing) {
                $attr = is_int($k) ? $callbackOrNothing : $k;
                $value = $this->get($attr);
                if (is_callable($callbackOrNothing)) {
                    $value = $callbackOrNothing($value, $this);
                }
                $result[$attr] = $value;
            }

            return $result;
        }

        throw new InvalidArgumentException('Expected string or array for get()');
    }

    public function __get(string $name): mixed
    {
        try {
            return $this->get($name);
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException("Property '{$name}' does not exist on ".static::class);
        }
    }

    public function __set(string $name, mixed $value): void
    {
        throw new \LogicException("Cannot set property '{$name}' on immutable ValueObject ".static::class);
    }
}
