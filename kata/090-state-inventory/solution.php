<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface InventoryState
{
    public function advance(InventoryWorkflow $workflow): void;
    public function name(): string;
}
final class DraftInventoryState implements InventoryState
{
    public function advance(InventoryWorkflow $w): void
    {
        $w->transitionTo(new ActiveInventoryState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveInventoryState implements InventoryState
{
    public function advance(InventoryWorkflow $w): void
    {
        $w->transitionTo(new CompletedInventoryState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedInventoryState implements InventoryState
{
    public function advance(InventoryWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class InventoryWorkflow
{
    public function __construct(private InventoryState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(InventoryState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new InventoryWorkflow(new DraftInventoryState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 90' . PHP_EOL;
