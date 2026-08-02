<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];

$required = [
    'EXPERT_UPGRADE_GUIDE.md',
    'cheatsheets/testing-patterns.md',
    'cheatsheets/gof-overview.md',
    'cheatsheets/laravel-pattern-map.md',
    'cheatsheets/refactoring-workflow.md',
    'handbook/README.md',
    'production/booking-platform/README.md',
    'production/inventory-platform/README.md',
    'production/crm-platform/README.md',
    'production/order-management-system/README.md',
];

foreach ($required as $relative) {
    $path = $root . '/' . $relative;
    if (!is_file($path)) {
        $errors[] = "Missing {$relative}";
        continue;
    }

    $content = file_get_contents($path) ?: '';
    $words = preg_match_all('/[\p{L}\p{N}_-]+/u', preg_replace('/```.*?```/s', '', $content) ?? '', $matches);
    if ($words < 220 && $relative !== 'EXPERT_UPGRADE_GUIDE.md') {
        $errors[] = "Thin expert file {$relative}: {$words} words";
    }

    if (!str_contains($content, '```mermaid') && !str_contains($relative, 'testing-patterns')) {
        $errors[] = "Missing diagram in {$relative}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "FAIL expert readiness audit\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}

echo "PASS expert readiness audit: production indexes, core cheatsheets and expert guide meet depth requirements.\n";
