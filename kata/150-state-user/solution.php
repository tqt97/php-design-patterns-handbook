<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface UserState
{
    public function advance(UserWorkflow $workflow): void;
    public function name(): string;
}
final class DraftUserState implements UserState
{
    public function advance(UserWorkflow $w): void
    {
        $w->transitionTo(new ActiveUserState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveUserState implements UserState
{
    public function advance(UserWorkflow $w): void
    {
        $w->transitionTo(new CompletedUserState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedUserState implements UserState
{
    public function advance(UserWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class UserWorkflow
{
    public function __construct(private UserState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(UserState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new UserWorkflow(new DraftUserState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 150' . PHP_EOL;
