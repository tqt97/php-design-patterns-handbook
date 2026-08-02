<?php

declare(strict_types=1);

interface PaymentGateway
{
    public function authorize(string $id): string;
}

final class InMemoryPaymentGateway implements PaymentGateway
{
    /** @var array<string, string> */
    private array $results = [];

    public function authorize(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return $this->results[$id] ??= 'payment-orchestrator:' . $id . ':ok';
    }
}

final readonly class PaymentOrchestratorApplication
{
    public function __construct(private PaymentGateway $port) {}

    public function run(string $id): string
    {
        return $this->port->authorize($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $app = new PaymentOrchestratorApplication(new InMemoryPaymentGateway());
    echo $app->run('demo'), PHP_EOL;
}
