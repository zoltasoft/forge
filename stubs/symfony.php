<?php

declare(strict_types=1);

namespace Twig {
    class Environment
    {
        public function render(string $name, array $context = []): string
        {
            return '';
        }
    }
}

namespace Symfony\Component\Security\Core\Exception {
    class AuthenticationException extends \RuntimeException
    {
        private ?string $token = null;

        public function getToken(): ?string
        {
            return $this->token;
        }

        public function getAttributes(): array
        {
            return [];
        }
    }

    class AccessDeniedException extends AuthenticationException
    {
        public function getAttributes(): array
        {
            return parent::getAttributes();
        }
    }
}

namespace Symfony\Component\Security\Core\Authorization {
    interface AuthorizationCheckerInterface
    {
        public function isGranted(string $attribute, mixed $subject = null): bool;
    }
}

namespace Symfony\Component\PasswordHasher\Hasher {
    interface PasswordHasherInterface
    {
        public function hash(string $plainPassword): string;

        public function verify(string $hashedPassword, string $plainPassword, array $options = []): bool;
    }
}

namespace Symfony\Component\Validator\Validator {
    use Symfony\Component\Validator\ConstraintViolationInterface;

    interface ValidatorInterface
    {
        /**
         * @return array<int, ConstraintViolationInterface>
         */
        public function validate(mixed $value, mixed $constraints = null, ?array $groups = null): array;
    }
}

namespace Symfony\Component\Validator {
    interface ValidatorInterface extends Validator\ValidatorInterface {}

    final class Validation
    {
        public static function createValidator(): Validator\ValidatorInterface
        {
            return new class implements Validator\ValidatorInterface
            {
                public function validate(mixed $value, mixed $constraints = null, ?array $groups = null): array
                {
                    return [];
                }
            };
        }
    }
}

namespace Symfony\Component\Validator\Constraints {
    class Constraint {}

    class Sequentially extends Constraint
    {
        public function __construct(public array $constraints = []) {}
    }

    class Email extends Constraint {}

    class Type extends Constraint
    {
        public function __construct(public string $type) {}
    }

    class Length extends Constraint
    {
        public function __construct(public ?int $min = null, public ?int $max = null) {}
    }
}

namespace Symfony\Component\Validator {
    interface ConstraintViolationInterface
    {
        public function getMessage(): string;

        public function getCode(): ?string;
    }
}
