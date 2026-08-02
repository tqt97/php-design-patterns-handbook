<?php

declare(strict_types=1);

interface Variant
{
    public function execute(string $id): string;
}

final class InMemoryVariant implements Variant
{
    /** @var array<string, string> */
    private array $results = [];

    public function execute(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return $this->results[$id] ??= 'feature-flag-rollout:' . $id . ':ok';
    }
}

final readonly class FeatureFlagRolloutApplication
{
    public function __construct(private Variant $port) {}

    public function run(string $id): string
    {
        return $this->port->execute($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $app = new FeatureFlagRolloutApplication(new InMemoryVariant());
    echo $app->run('demo'), PHP_EOL;
}
