<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];

$required = [
    'README.md', 'CHANGELOG.md', 'MANIFEST.md', 'OVERVIEW.md', 'REVIEW_REPORT.md',
    'DIRECTORY_QUALITY_MATRIX.md', 'RELEASE_CHECKLIST.md', 'AUDIT_CYCLES.md',
];

foreach ($required as $file) {
    $path = $root . '/' . $file;
    if (!is_file($path)) {
        $errors[] = "Missing release file: {$file}";
    }
}

$changelog = file_get_contents($root . '/CHANGELOG.md') ?: '';
if (preg_match('/^##\s*\[?v?\d+\.\d+/mi', $changelog)) {
    $errors[] = 'CHANGELOG.md must be cumulative and must not contain version headings.';
}

$readme = file_get_contents($root . '/README.md') ?: '';
foreach (['DIRECTORY_QUALITY_MATRIX.md', 'RELEASE_CHECKLIST.md', 'CHANGELOG.md'] as $link) {
    if (!str_contains($readme, $link)) {
        $errors[] = "README.md must link to {$link}";
    }
}

foreach (['production/payment-platform/README.md', 'production/notification-platform/README.md', 'production/booking-platform/README.md', 'production/inventory-platform/README.md', 'production/crm-platform/README.md', 'production/order-management-system/README.md'] as $file) {
    $content = file_get_contents($root . '/' . $file) ?: '';
    foreach (['## Source of truth', '## Invariant xuyên hệ thống', '```mermaid', '## Failure model', '## Test strategy', '## Observability', '## Definition of Done'] as $section) {
        if (!str_contains($content, $section)) {
            $errors[] = "{$file} is missing {$section}";
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "FAIL final release audit:\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}

echo "PASS final release audit: cumulative changelog, release docs and platform depth verified.\n";
