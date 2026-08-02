<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface OrderState
{
    public function advance(OrderWorkflow $workflow): void;
    public function name(): string;
}
final class DraftOrderState implements OrderState
{
    public function advance(OrderWorkflow $w): void
    {
        $w->transitionTo(new ActiveOrderState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveOrderState implements OrderState
{
    public function advance(OrderWorkflow $w): void
    {
        $w->transitionTo(new CompletedOrderState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedOrderState implements OrderState
{
    public function advance(OrderWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class OrderWorkflow
{
    public function __construct(private OrderState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(OrderState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new OrderWorkflow(new DraftOrderState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 174' . PHP_EOL;
