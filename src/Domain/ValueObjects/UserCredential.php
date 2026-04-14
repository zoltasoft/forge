<?php

namespace Zolta\Domain\ValueObjects;

final class UserCredential extends ValueObject
{
    protected array $getters = ['email', 'password'];

    public function __construct(
        protected Email $email,
        protected Password $password,
        protected ?VOConstructionContext $context = null
    ) {
        parent::__construct();
    }
}
