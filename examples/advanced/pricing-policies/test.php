<?php

declare(strict_types=1);

require __DIR__ . '/after.php';

$result = (new PricingPoliciesUseCase(new InMemoryPricePolicy()))->handle('case-42');
assert($result === 'pricing:case-42:ok');

echo 'PASS pricing-policies', PHP_EOL;
