<?php

declare(strict_types=1);

namespace Tests\Unit\Behavioral\State;

use DesignPatterns\Behavioral\State\Order;
use DomainException;
use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    public function test_valid_order_lifecycle(): void
    {
        $order = new Order();
        $order->pay();
        $order->ship();
        self::assertSame('shipped', $order->state());
    }

    public function test_pending_order_cannot_ship(): void
    {
        $this->expectException(DomainException::class);
        (new Order())->ship();
    }
    public function test_pending_order_can_be_cancelled_but_not_paid_afterwards(): void
    {
        $order = new Order();
        $order->cancel();
        self::assertSame('cancelled', $order->state());

        $this->expectException(DomainException::class);
        $order->pay();
    }

    public function test_paid_order_requires_refund_workflow_before_cancellation(): void
    {
        $order = new Order();
        $order->pay();

        $this->expectException(DomainException::class);
        $order->cancel();
    }

    public function test_shipped_order_cannot_transition_back_to_cancelled(): void
    {
        $order = new Order();
        $order->pay();
        $order->ship();

        $this->expectException(DomainException::class);
        $order->cancel();
    }

}
