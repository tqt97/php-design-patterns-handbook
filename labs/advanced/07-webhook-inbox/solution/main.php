<?php

declare(strict_types=1);

interface WebhookInboxPort
{
    public function execute(string $id): string;
}

final class InMemoryWebhookInboxPort implements WebhookInboxPort
{
    public function execute(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }
        return '07-webhook-inbox:' . $id . ':ok';
    }
}

final readonly class WebhookInbox
{
    public function __construct(private WebhookInboxPort $port)
    {
    }
    public function execute(string $id): string
    {
        return $this->port->execute($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new WebhookInbox(new InMemoryWebhookInboxPort()))->execute('demo'), PHP_EOL;
}
