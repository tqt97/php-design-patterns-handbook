<?php

declare(strict_types=1);

interface ProductSource
{
    public function find(string $id): string;
}

final class InMemoryProductSource implements ProductSource
{
    /** @var array<string, string> */
    private array $results = [];

    public function find(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return $this->results[$id] ??= 'cache-aside-catalog:' . $id . ':ok';
    }
}

final readonly class CacheAsideCatalogApplication
{
    public function __construct(private ProductSource $port) {}

    public function run(string $id): string
    {
        return $this->port->find($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $app = new CacheAsideCatalogApplication(new InMemoryProductSource());
    echo $app->run('demo'), PHP_EOL;
}
