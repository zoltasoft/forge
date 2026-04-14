<?php

namespace Zolta\Domain\ValueObjects;

final class VOConstructionContext
{
    /** @var array<string, callable> */
    public array $runtimePreprocessors = [];

    /** @var array<string, mixed> */
    public array $runtimeOptions = [];

    public bool $skipResolve = false;

    /**
     * @param  array<string, callable>  $runtimePreprocessors
     * @param  array<string, mixed>  $runtimeOptions
     */
    public function __construct(
        array $runtimePreprocessors = [],
        array $runtimeOptions = [],
        bool $skipResolve = false
    ) {
        $this->runtimePreprocessors = $runtimePreprocessors;
        $this->runtimeOptions = $runtimeOptions;
        $this->skipResolve = $skipResolve;
    }
}
