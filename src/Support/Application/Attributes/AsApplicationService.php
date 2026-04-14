<?php

declare(strict_types=1);

namespace Zolta\Support\Application\Attributes;

/**
 * Optional marker attribute to tag application services for CQRS autoconfiguration.
 *
 * This is additive to the AppServiceInterface marker; either can be used to
 * produce the same container tagging (`zolta.cqrs.app_service`).
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AsApplicationService
{
    public function __construct() {}
}
