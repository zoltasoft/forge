<?php

declare(strict_types=1);

namespace Zolta\Support\Traits;

use Illuminate\Contracts\Container\Container;
use Zolta\Support\Contracts\NormalizerInterface;

trait Normalizable
{
    /** @var callable|null */
    protected static $normalizerFactory = null;

    protected static ?NormalizerInterface $normalizerInstance = null;

    /**
     * Set a factory that returns a NormalizerInterface instance.
     *
     * Example: Normalizable::setNormalizerFactory(fn() => app(NormalizerInterface::class));
     */
    public static function setNormalizerFactory(callable $factory): void
    {
        static::$normalizerFactory = $factory;
        // reset cached instance so factory takes effect
        static::$normalizerInstance = null;
    }

    /** Resolve (and cache) the normalizer instance */
    protected static function getNormalizerInstance(): NormalizerInterface
    {
        if (static::$normalizerInstance instanceof NormalizerInterface) {
            return static::$normalizerInstance;
        }

        // 1) If factory exists, call it
        if (is_callable(static::$normalizerFactory)) {
            $instance = call_user_func(static::$normalizerFactory);
            if (! $instance instanceof NormalizerInterface) {
                throw new \RuntimeException('Normalizer factory did not return an instance of NormalizerInterface.');
            }
            static::$normalizerInstance = $instance;

            return $instance;
        }

        // 2) Try resolving from Laravel container, if available
        if (function_exists('app')) {
            try {
                $app = app();
                if ($app instanceof Container && $app->bound(NormalizerInterface::class)) {
                    $instance = $app->make(NormalizerInterface::class);
                    if ($instance instanceof NormalizerInterface) {
                        static::$normalizerInstance = $instance;

                        return $instance;
                    }
                }
            } catch (\Throwable) {
                // swallow and throw below with useful message
            }
        }

        // 3) Fallback to Symfony normalizer if available (non-Laravel environments)
        if (class_exists(\Zolta\Support\Serialization\Normalizer::class)) {
            $instance = new \Zolta\Support\Serialization\Normalizer;
            static::$normalizerInstance = $instance;

            return $instance;
        }

        // Nothing found — throw clear message
        throw new \RuntimeException(
            'No NormalizerInterface implementation bound. '.
            'Make sure the NormalizerServiceProvider is registered OR call '.
            'Normalizable::setNormalizerFactory(fn() => app(\\Zolta\\Support\\Contracts\\NormalizerInterface::class)).'
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return static::getNormalizerInstance()->normalize($this);
    }
}
