<?php

declare(strict_types=1);

namespace Tests\Unit\ReadModel;

use DesignPatterns\ReadModel\Page;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PageTest extends TestCase
{
    public function testItExposesItemsAndCursorState(): void
    {
        $page = new Page(['a', 'b'], 'cursor-2');

        self::assertSame(['a', 'b'], $page->items);
        self::assertTrue($page->hasNextPage());
    }

    public function testNullCursorMeansLastPage(): void
    {
        self::assertFalse((new Page([], null))->hasNextPage());
    }

    public function testItRejectsEmptyCursor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Page([], '');
    }
}
