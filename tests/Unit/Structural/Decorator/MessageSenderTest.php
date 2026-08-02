<?php

declare(strict_types=1);

namespace Tests\Unit\Structural\Decorator;

use DesignPatterns\Structural\Decorator\InMemoryMessageSender;
use DesignPatterns\Structural\Decorator\ValidatingMessageSender;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MessageSenderTest extends TestCase
{
    public function test_validation_decorator_delegates_valid_message(): void
    {
        $inner = new InMemoryMessageSender();
        (new ValidatingMessageSender($inner))->send('room-1', 'Deployment completed');
        self::assertCount(1, $inner->sent());
    }

    public function test_validation_decorator_rejects_blank_message(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new ValidatingMessageSender(new InMemoryMessageSender()))->send('room-1', '');
    }
}
