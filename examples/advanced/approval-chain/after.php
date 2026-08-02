<?php

declare(strict_types=1);

interface Approver
{
    public function approve(string $id): string;
}

final class InMemoryApprover implements Approver
{
    public function approve(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return 'approval:' . $id . ':ok';
    }
}

final readonly class ApprovalChainUseCase
{
    public function __construct(private Approver $component)
    {
    }

    public function handle(string $id): string
    {
        return $this->component->approve($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new ApprovalChainUseCase(new InMemoryApprover()))->handle('demo-1'), PHP_EOL;
}
