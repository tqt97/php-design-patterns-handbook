<?php

declare(strict_types=1);

interface LegacyAPIAdapterPort
{
    public function execute(string $id): string;
}

final class InMemoryLegacyAPIAdapterPort implements LegacyAPIAdapterPort
{
    public function execute(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }
        return '06-legacy-api-adapter:' . $id . ':ok';
    }
}

final readonly class LegacyAPIAdapter
{
    public function __construct(private LegacyAPIAdapterPort $port)
    {
    }
    public function execute(string $id): string
    {
        return $this->port->execute($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new LegacyAPIAdapter(new InMemoryLegacyAPIAdapterPort()))->execute('demo'), PHP_EOL;
}
