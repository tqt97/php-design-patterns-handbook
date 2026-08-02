<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CheckoutState
{
    public function advance(CheckoutWorkflow $workflow): void;
    public function name(): string;
}
final class DraftCheckoutState implements CheckoutState
{
    public function advance(CheckoutWorkflow $w): void
    {
        $w->transitionTo(new ActiveCheckoutState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveCheckoutState implements CheckoutState
{
    public function advance(CheckoutWorkflow $w): void
    {
        $w->transitionTo(new CompletedCheckoutState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedCheckoutState implements CheckoutState
{
    public function advance(CheckoutWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class CheckoutWorkflow
{
    public function __construct(private CheckoutState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(CheckoutState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new CheckoutWorkflow(new DraftCheckoutState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 18' . PHP_EOL;
