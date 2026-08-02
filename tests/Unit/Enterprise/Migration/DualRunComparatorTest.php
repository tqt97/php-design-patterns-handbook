<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise\Migration;

use DesignPatterns\Enterprise\Migration\DualRunComparator;
use PHPUnit\Framework\TestCase;

final class DualRunComparatorTest extends TestCase
{
    public function testNormalizesNonSemanticFieldsBeforeComparison(): void
    {
        $comparator = new DualRunComparator(
            authoritative: static fn (int $amount): array => ['amount' => $amount, 'generatedAt' => 'old'],
            shadow: static fn (int $amount): array => ['amount' => $amount, 'generatedAt' => 'new'],
            normalizer: static fn (array $result): array => ['amount' => $result['amount']],
        );

        self::assertTrue($comparator->compare(100)->equivalent());
    }
}
