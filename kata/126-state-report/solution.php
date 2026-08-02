<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface ReportState
{
    public function advance(ReportWorkflow $workflow): void;
    public function name(): string;
}
final class DraftReportState implements ReportState
{
    public function advance(ReportWorkflow $w): void
    {
        $w->transitionTo(new ActiveReportState());
    }
    public function name(): string
    {
        return 'draft';
    }
}
final class ActiveReportState implements ReportState
{
    public function advance(ReportWorkflow $w): void
    {
        $w->transitionTo(new CompletedReportState());
    }
    public function name(): string
    {
        return 'active';
    }
}
final class CompletedReportState implements ReportState
{
    public function advance(ReportWorkflow $w): void
    {
        throw new DomainException('completed');
    }
    public function name(): string
    {
        return 'completed';
    }
}
final class ReportWorkflow
{
    public function __construct(private ReportState $state)
    {
    }
    public function advance(): void
    {
        $this->state->advance($this);
    }
    public function transitionTo(ReportState $s): void
    {
        $this->state = $s;
    }
    public function status(): string
    {
        return $this->state->name();
    }
}
$w = new ReportWorkflow(new DraftReportState());
$w->advance();
expect($w->status() === 'active', 'first transition');
$w->advance();
expect($w->status() === 'completed', 'second transition');
echo 'PASS kata 126' . PHP_EOL;
