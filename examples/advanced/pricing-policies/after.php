<?php

declare(strict_types=1);

interface PricePolicy
{
    public function calculate(string $id): string;
}

final class InMemoryPricePolicy implements PricePolicy
{
    public function calculate(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return 'pricing:' . $id . ':ok';
    }
}

final readonly class PricingPoliciesUseCase
{
    public function __construct(private PricePolicy $component) {}

    public function handle(string $id): string
    {
        return $this->component->calculate($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new PricingPoliciesUseCase(new InMemoryPricePolicy()))->handle('demo-1'), PHP_EOL;
}
