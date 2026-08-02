<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$readme = file_get_contents($root . '/src/README.md');
if ($readme === false) {
    fwrite(STDERR, "Missing src/README.md\n");
    exit(1);
}

$requiredAreas = [
    'Domain/', 'Application/Command/', 'Creational/Factory/',
    'Structural/Adapter/', 'Structural/Decorator/',
    'Behavioral/Strategy/', 'Behavioral/Observer/', 'Behavioral/State/',
    'Enterprise/Repository/', 'Enterprise/Query/', 'Enterprise/Specification/',
    'Enterprise/UnitOfWork/', 'Infrastructure/Idempotency/',
    'Infrastructure/Outbox/', 'ReadModel/',
];

$errors = [];
foreach ($requiredAreas as $area) {
    if (! str_contains($readme, $area)) {
        $errors[] = "Source map missing area: {$area}";
    }
}

$phpFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src'));
foreach ($phpFiles as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $content = file_get_contents($file->getPathname());
    if ($content === false || ! str_contains($content, 'declare(strict_types=1);')) {
        $errors[] = 'Missing strict_types: ' . $file->getPathname();
    }
}

if ($errors !== []) {
    fwrite(STDERR, implode("\n", $errors) . "\n");
    exit(1);
}

echo "PASS source map audit: documented source areas and strict types verified\n";
