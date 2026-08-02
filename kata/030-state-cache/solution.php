<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CacheState
{
    public function advance(CacheWorkflow $workflow): void;
    public function name(): string;
}
final class DraftCacheState implements CacheState
{
    public function advance(CacheWorkflow $w): void
    {
        $w->transitionTo(new ActiveCacheState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveCacheState implements CacheState
{
    public function advance(CacheWorkflow $w): void
    {
        $w->transitionTo(new CompletedCacheState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedCacheState implements CacheState
{
    public function advance(CacheWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class CacheWorkflow
{
    public function __construct(private CacheState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(CacheState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new CacheWorkflow(new DraftCacheState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 30' . PHP_EOL;
