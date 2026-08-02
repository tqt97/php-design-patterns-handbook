<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CsvState
{
    public function advance(CsvWorkflow $workflow): void;
    public function name(): string;
}
final class DraftCsvState implements CsvState
{
    public function advance(CsvWorkflow $w): void
    {
        $w->transitionTo(new ActiveCsvState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveCsvState implements CsvState
{
    public function advance(CsvWorkflow $w): void
    {
        $w->transitionTo(new CompletedCsvState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedCsvState implements CsvState
{
    public function advance(CsvWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class CsvWorkflow
{
    public function __construct(private CsvState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(CsvState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new CsvWorkflow(new DraftCsvState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 66' . PHP_EOL;
