<?php

declare(strict_types=1);

namespace DesignPatterns\ReadModel;

use InvalidArgumentException;

/** @template T */
final readonly class Page
{
    /**
     * @param list<T> $items
     */
    public function __construct(
        public array $items,
        public ?string $nextCursor,
    ) {
        if ($nextCursor === '') {
            throw new InvalidArgumentException('Next cursor must be null or a non-empty string.');
        }
    }

    public function hasNextPage(): bool
    {
        return $this->nextCursor !== null;
    }
}
