<?php

declare(strict_types=1);

interface Inbox
{
    public function accept(string $id): string;
}

final class InMemoryInbox implements Inbox
{
    public function accept(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return 'integration:' . $id . ':ok';
    }
}

final readonly class WebhookInboxUseCase
{
    public function __construct(private Inbox $component) {}

    public function handle(string $id): string
    {
        return $this->component->accept($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new WebhookInboxUseCase(new InMemoryInbox()))->handle('demo-1'), PHP_EOL;
}
