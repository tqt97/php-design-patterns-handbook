<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface EmailState
{
    public function advance(EmailWorkflow $workflow): void;
    public function name(): string;
}
final class DraftEmailState implements EmailState
{
    public function advance(EmailWorkflow $w): void
    {
        $w->transitionTo(new ActiveEmailState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveEmailState implements EmailState
{
    public function advance(EmailWorkflow $w): void
    {
        $w->transitionTo(new CompletedEmailState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedEmailState implements EmailState
{
    public function advance(EmailWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class EmailWorkflow
{
    public function __construct(private EmailState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(EmailState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new EmailWorkflow(new DraftEmailState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 42' . PHP_EOL;
