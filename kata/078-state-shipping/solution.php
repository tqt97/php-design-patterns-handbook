<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface ShippingState
{
    public function advance(ShippingWorkflow $workflow): void;
    public function name(): string;
}
final class DraftShippingState implements ShippingState
{
    public function advance(ShippingWorkflow $w): void
    {
        $w->transitionTo(new ActiveShippingState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveShippingState implements ShippingState
{
    public function advance(ShippingWorkflow $w): void
    {
        $w->transitionTo(new CompletedShippingState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedShippingState implements ShippingState
{
    public function advance(ShippingWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class ShippingWorkflow
{
    public function __construct(private ShippingState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(ShippingState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new ShippingWorkflow(new DraftShippingState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 78' . PHP_EOL;
