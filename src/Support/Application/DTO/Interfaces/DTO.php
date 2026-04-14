<?php

declare(strict_types=1);

namespace Zolta\Support\Application\DTO\Interfaces;

/**
 * Marker interface for all application-level DTOs.
 *
 * Framework-agnostic: lives in zolta-forge so Application layer classes
 * can implement it without depending on any HTTP or presentation package.
 */
interface DTO {}
