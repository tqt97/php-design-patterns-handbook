<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface AuditState
{
    public function advance(AuditWorkflow $workflow): void;
    public function name(): string;
}
final class DraftAuditState implements AuditState
{
    public function advance(AuditWorkflow $w): void
    {
        $w->transitionTo(new ActiveAuditState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveAuditState implements AuditState
{
    public function advance(AuditWorkflow $w): void
    {
        $w->transitionTo(new CompletedAuditState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedAuditState implements AuditState
{
    public function advance(AuditWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class AuditWorkflow
{
    public function __construct(private AuditState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(AuditState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new AuditWorkflow(new DraftAuditState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 6' . PHP_EOL;
