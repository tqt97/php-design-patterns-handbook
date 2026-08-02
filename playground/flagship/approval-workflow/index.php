<?php

declare(strict_types=1);

interface ApprovalPolicy
{
    public function approve(string $id): string;
}

final class InMemoryApprovalPolicy implements ApprovalPolicy
{
    /** @var array<string, string> */
    private array $results = [];

    public function approve(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return $this->results[$id] ??= 'approval-workflow:' . $id . ':ok';
    }
}

final readonly class ApprovalWorkflowApplication
{
    public function __construct(private ApprovalPolicy $port) {}

    public function run(string $id): string
    {
        return $this->port->approve($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $app = new ApprovalWorkflowApplication(new InMemoryApprovalPolicy());
    echo $app->run('demo'), PHP_EOL;
}
