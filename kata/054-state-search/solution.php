<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface SearchState
{
    public function advance(SearchWorkflow $workflow): void;
    public function name(): string;
}
final class DraftSearchState implements SearchState
{
    public function advance(SearchWorkflow $w): void
    {
        $w->transitionTo(new ActiveSearchState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveSearchState implements SearchState
{
    public function advance(SearchWorkflow $w): void
    {
        $w->transitionTo(new CompletedSearchState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedSearchState implements SearchState
{
    public function advance(SearchWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class SearchWorkflow
{
    public function __construct(private SearchState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(SearchState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new SearchWorkflow(new DraftSearchState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 54' . PHP_EOL;
