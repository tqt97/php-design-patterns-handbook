<?php

declare(strict_types=1);

interface Channel
{
    public function send(string $id): string;
}

final class InMemoryChannel implements Channel
{
    public function send(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return 'notification:' . $id . ':ok';
    }
}

final readonly class NotificationFallbackUseCase
{
    public function __construct(private Channel $component) {}

    public function handle(string $id): string
    {
        return $this->component->send($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new NotificationFallbackUseCase(new InMemoryChannel()))->handle('demo-1'), PHP_EOL;
}
