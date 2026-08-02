<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise\Specification;

use DesignPatterns\Enterprise\Specification\AndSpecification;
use DesignPatterns\Enterprise\Specification\Specification;
use PHPUnit\Framework\TestCase;

final class AndSpecificationTest extends TestCase
{
    public function testItRequiresEverySpecificationToBeSatisfied(): void
    {
        $positive = new class implements Specification {
            public function isSatisfiedBy(object $candidate): bool { return $candidate->value > 0; }
        };
        $even = new class implements Specification {
            public function isSatisfiedBy(object $candidate): bool { return $candidate->value % 2 === 0; }
        };

        $specification = new AndSpecification([$positive, $even]);

        self::assertTrue($specification->isSatisfiedBy((object) ['value' => 2]));
        self::assertFalse($specification->isSatisfiedBy((object) ['value' => 3]));
    }
}
