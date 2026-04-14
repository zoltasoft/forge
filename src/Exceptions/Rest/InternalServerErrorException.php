<?php

declare(strict_types=1);

namespace Zolta\Exceptions\Rest;

use Throwable;
use Zolta\Exceptions\BaseException;

final class InternalServerErrorException extends BaseException
{
    public function __construct(private readonly ?Throwable $previousThrowable = null)
    {
        // Provide: previous, errorCode (string|null), context (array), status (int|null)
        parent::__construct(
            $this->previousThrowable,
            null, // no symbolic errorCode by default
            [],   // context is handled by context() method override below
            $this->status() // integer 500
        );
    }

    protected function exceptionMessage(): string
    {
        return 'Internal server error';
    }

    public function context(): array
    {
        $context = [
            'public' => [
                'code' => 'server.error',
            ],
        ];

        if ($this->previousThrowable instanceof \Throwable) {
            $context['debug'] = [
                'previous_message' => $this->previousThrowable->getMessage(),
                'previous_type' => $this->previousThrowable::class,
            ];
        }

        return $context;
    }

    public function status(): int
    {
        return 500;
    }
}
