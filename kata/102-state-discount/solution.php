<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface DiscountState
{
    public function advance(DiscountWorkflow $workflow): void;
    public function name(): string;
}
final class DraftDiscountState implements DiscountState
{
    public function advance(DiscountWorkflow $w): void
    {
        $w->transitionTo(new ActiveDiscountState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveDiscountState implements DiscountState
{
    public function advance(DiscountWorkflow $w): void
    {
        $w->transitionTo(new CompletedDiscountState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedDiscountState implements DiscountState
{
    public function advance(DiscountWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class DiscountWorkflow
{
    public function __construct(private DiscountState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(DiscountState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new DiscountWorkflow(new DraftDiscountState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 102' . PHP_EOL;
