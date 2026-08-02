<?php

declare(strict_types=1);

interface CustomerQuery
{
    public function search(string $id): string;
}

final class InMemoryCustomerQuery implements CustomerQuery
{
    public function search(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return 'crm:' . $id . ':ok';
    }
}

final readonly class CRMQueryObjectUseCase
{
    public function __construct(private CustomerQuery $component)
    {
    }

    public function handle(string $id): string
    {
        return $this->component->search($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new CRMQueryObjectUseCase(new InMemoryCustomerQuery()))->handle('demo-1'), PHP_EOL;
}
