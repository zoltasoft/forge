<?php

declare(strict_types=1);

namespace Zolta\Domain\Exceptions;

use Exception;
use Throwable;
use Zolta\Domain\Exceptions\Contracts\RenderableExceptionInterface;
use Zolta\Domain\Exceptions\Traits\RenderableExceptionTrait;

/**
 * Domain base exception — pure, side-effect free.
 *
 * Constructor:
 *   __construct(?Throwable $previous = null, ?string $errorCode = null, array $context = [], ?int $status = null)
 *
 * - $previous: previous throwable chain
 * - $errorCode: optional symbolic string code (kept separate from the int status)
 * - $context: optional structured context
 * - $status: optional integer status/code that will be passed to Exception::__construct()
 *
 * If $status is not provided, we derive the int code from previous->getCode() if it's an int,
 * otherwise fallback to 400.
 */
abstract class BaseException extends Exception implements RenderableExceptionInterface
{
    use RenderableExceptionTrait;

    /** Optional explicit status override (int) */
    protected ?int $statusOverride = null;

    /** Each concrete exception must provide the user-facing message. */
    abstract protected function exceptionMessage(): string;

    /**
     * @param  string|null  $errorCode  optional symbolic code
     * @param  array<string, mixed>  $context  optional structured context
     * @param  int|null  $status  optional integer status (overrides previous code)
     */
    public function __construct(?Throwable $previous = null, protected ?string $errorCode = null, protected array $context = [], ?int $status = null)
    {
        $message = $this->exceptionMessage();

        // Determine an integer code to pass to Exception::__construct()
        if ($status !== null) {
            $intCode = $status;
        } else {
            $rawPrevCode = $previous?->getCode() ?? 0;
            $intCode = is_int($rawPrevCode) ? $rawPrevCode : (int) $rawPrevCode;
            // If still 0, use 400 as a reasonable default
            if ($intCode === 0) {
                $intCode = 400;
            }
        }

        // Call native Exception constructor with safe int code
        parent::__construct($message, $intCode, $previous);
        $this->statusOverride = $status;
    }

    /**
     * Domain/application logical status.
     * Use explicit override if provided; otherwise use the native exception code or default 400.
     */
    public function status(): int
    {
        if ($this->statusOverride !== null) {
            return $this->statusOverride;
        }

        $code = $this->getCode();

        return $code ?: 400;
    }

    /**
     * Optional symbolic code (string) for API clients / logs.
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * Structured context for infra.
     */
    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return $this->context;
    }

    public function toErrorArray(): array
    {
        $base = [
            'type' => $this->type(),
            'message' => $this->getMessage(),
            'context' => $this->context(),
        ];

        if ($this->errorCode !== null) {
            $base['error_code'] = $this->errorCode;
        }

        return $base;
    }
}
