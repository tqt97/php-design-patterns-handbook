<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CrmState
{
    public function advance(CrmWorkflow $workflow): void;
    public function name(): string;
}
final class DraftCrmState implements CrmState
{
    public function advance(CrmWorkflow $w): void
    {
        $w->transitionTo(new ActiveCrmState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveCrmState implements CrmState
{
    public function advance(CrmWorkflow $w): void
    {
        $w->transitionTo(new CompletedCrmState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedCrmState implements CrmState
{
    public function advance(CrmWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class CrmWorkflow
{
    public function __construct(private CrmState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(CrmState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new CrmWorkflow(new DraftCrmState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 138' . PHP_EOL;
