<?php

namespace Zolta\Domain\ValueObjects;

use InvalidArgumentException;
use Zolta\Domain\Interfaces\VO;

final class AvatarUrl extends ValueObject
{
    protected array $getters = ['url'];

    protected string $url;

    private const MAX_LENGTH = 2048;

    public function __construct(string $url, ?VOConstructionContext $context = null)
    {
        $url = trim($url);
        if ($url === '') {
            throw new InvalidArgumentException('Avatar URL cannot be empty.');
        }

        if (mb_strlen($url) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf('Avatar URL cannot exceed %d characters.', self::MAX_LENGTH));
        }

        // Allow absolute URLs or local paths starting with /
        $isAbsolute = filter_var($url, FILTER_VALIDATE_URL) !== false;
        $isLocal = str_starts_with($url, '/');

        if (! $isAbsolute && ! $isLocal) {
            throw new InvalidArgumentException('Avatar URL must be an absolute URL or a local path starting with "/".');
        }

        $resolved = self::resolveInternal(['url' => $url], $context);
        $this->url = $resolved['url'];
    }

    public function equals(VO $other): bool
    {
        return $other instanceof self && $this->url === $other->url;
    }
}
