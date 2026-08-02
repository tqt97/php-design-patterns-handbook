<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface SupportState
{
    public function advance(SupportWorkflow $workflow): void;
    public function name(): string;
}
final class DraftSupportState implements SupportState
{
    public function advance(SupportWorkflow $w): void
    {
        $w->transitionTo(new ActiveSupportState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveSupportState implements SupportState
{
    public function advance(SupportWorkflow $w): void
    {
        $w->transitionTo(new CompletedSupportState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedSupportState implements SupportState
{
    public function advance(SupportWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class SupportWorkflow
{
    public function __construct(private SupportState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(SupportState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new SupportWorkflow(new DraftSupportState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 186' . PHP_EOL;
