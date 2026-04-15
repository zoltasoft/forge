<?php

declare(strict_types=1);

namespace Zolta\Domain\Policies;

use Zolta\Domain\Contracts\Policy;
use Zolta\Domain\Interfaces\VO;
use Zolta\Domain\ValueObjects\Email;

final class EmailPolicy extends Policy
{
    public function apply(VO $vo, array $options = []): mixed
    {
        // type check
        if (! ($vo instanceof Email)) {
            throw new \InvalidArgumentException('EmailPolicy expects Email VO');
        }

        /** @var Email $vo */
        // Example: if policy requires verified emails, enforce it
        $requireVerified = $options['requireVerified'] ?? false;
        if ($requireVerified && $vo->get('verifiedAt') === null) {
            throw new \DomainException('Policy requires email to be verified');
        }

        // Example: if policy should mark trusted domain, you could return metadata
        $trusted = $options['trustedDomains'] ?? [];
        $addr = $vo->get('address');
        $domain = (str_contains((string) $addr, '@')) ? substr(strrchr((string) $addr, '@'), 1) : null;
        if ($domain !== null && in_array($domain, (array) $trusted, true)) {
            // return some metadata or side-effect decision
            return ['trusted' => true, 'domain' => $domain];
        }

        return null;
    }
}
