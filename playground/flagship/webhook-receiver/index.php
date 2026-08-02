<?php

declare(strict_types=1);

interface WebhookHandler
{
    public function handle(string $id): string;
}

final class InMemoryWebhookHandler implements WebhookHandler
{
    /** @var array<string, string> */
    private array $results = [];

    public function handle(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return $this->results[$id] ??= 'webhook-receiver:' . $id . ':ok';
    }
}

final readonly class WebhookReceiverApplication
{
    public function __construct(private WebhookHandler $port) {}

    public function run(string $id): string
    {
        return $this->port->handle($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $app = new WebhookReceiverApplication(new InMemoryWebhookHandler());
    echo $app->run('demo'), PHP_EOL;
}
