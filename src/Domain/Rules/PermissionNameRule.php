<?php

declare(strict_types=1);

namespace Zolta\Domain\Rules;

use InvalidArgumentException;
use Zolta\Domain\Contracts\Rule;

/**
 * Rule to ensure a permission name follows a consistent naming convention.
 *
 * Examples of valid permission names:
 *  - users.read
 *  - manage_users
 *  - roles.assign
 */
final class PermissionNameRule extends Rule
{
    private const PATTERN = '/^[a-z][a-z0-9_\.]{2,99}$/'; // starts with a-z, then letters/numbers/underscores/dots

    /**
     * @param  string  $fieldName  Used for exception message context
     */
    public function __construct(
        private readonly string $fieldName = 'permission name'
    ) {}

    /**
     * Validate the given value.
     *
     * @throws InvalidArgumentException
     */
    public function validate(mixed $value, array $options = []): void
    {
        if (! preg_match(self::PATTERN, (string) $value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid %s format. Must start with a lowercase letter and contain only lowercase letters, numbers, underscores, or dots.',
                    $this->fieldName
                )
            );
        }
    }
}
