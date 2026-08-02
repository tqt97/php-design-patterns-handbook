<?php

declare(strict_types=1);

interface OrderStep
{
    public function run(string $id): string;
}

final class InMemoryOrderStep implements OrderStep
{
    /** @var array<string, string> */
    private array $results = [];

    public function run(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return $this->results[$id] ??= 'order-workflow:' . $id . ':ok';
    }
}

final readonly class OrderWorkflowApplication
{
    public function __construct(private OrderStep $port) {}

    public function run(string $id): string
    {
        return $this->port->run($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $app = new OrderWorkflowApplication(new InMemoryOrderStep());
    echo $app->run('demo'), PHP_EOL;
}
