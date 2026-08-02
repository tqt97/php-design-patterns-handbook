<?php

declare(strict_types=1);

interface LeadPolicy
{
    public function evaluate(string $id): string;
}

final class InMemoryLeadPolicy implements LeadPolicy
{
    /** @var array<string, string> */
    private array $results = [];

    public function evaluate(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return $this->results[$id] ??= 'crm-follow-up:' . $id . ':ok';
    }
}

final readonly class CRMFollowUpApplication
{
    public function __construct(private LeadPolicy $port) {}

    public function run(string $id): string
    {
        return $this->port->evaluate($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $app = new CRMFollowUpApplication(new InMemoryLeadPolicy());
    echo $app->run('demo'), PHP_EOL;
}
