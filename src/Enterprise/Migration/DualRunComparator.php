<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Migration;

use Closure;

final readonly class DualRunComparator
{
    /** @var Closure(mixed): mixed */
    private Closure $authoritative;

    /** @var Closure(mixed): mixed */
    private Closure $shadow;

    /** @var Closure(mixed): mixed */
    private Closure $normalizer;

    /**
     * @param callable(mixed): mixed $authoritative
     * @param callable(mixed): mixed $shadow
     * @param callable(mixed): mixed|null $normalizer
     */
    public function __construct(
        callable $authoritative,
        callable $shadow,
        ?callable $normalizer = null,
    ) {
        $this->authoritative = Closure::fromCallable($authoritative);
        $this->shadow = Closure::fromCallable($shadow);
        $this->normalizer = $normalizer === null
            ? static fn (mixed $value): mixed => $value
            : Closure::fromCallable($normalizer);
    }

    public function compare(mixed $input): ComparisonResult
    {
        $authoritative = ($this->authoritative)($input);
        $shadow = ($this->shadow)($input);
        $left = ($this->normalizer)($authoritative);
        $right = ($this->normalizer)($shadow);

        return new ComparisonResult(
            authoritative: $authoritative,
            shadow: $shadow,
            differences: $left === $right ? [] : ['authoritative' => $left, 'shadow' => $right],
        );
    }
}
