<?php

declare(strict_types=1);

$root = dirname(__DIR__) . '/exercises';
$files = glob($root . '/module-*/README.md') ?: [];
$errors = [];
$designs = [];

foreach ($files as $file) {
    $content = file_get_contents($file);
    if ($content === false) {
        $errors[] = "Cannot read {$file}";
        continue;
    }

    if (!preg_match('/^## Mô hình thiết kế cần hướng tới\s*$([\s\S]*?)(?=^## Nhiệm vụ)/m', $content, $match)) {
        $errors[] = "Missing design-model section: {$file}";
        continue;
    }

    $section = trim($match[1]);
    if (!str_contains($section, '```mermaid')) {
        $errors[] = "Missing Mermaid diagram: {$file}";
    }

    foreach (['FirstImplementation', 'SecondImplementation', 'Source of truth', 'External side effect'] as $generic) {
        if (str_contains($section, $generic)) {
            $errors[] = "Generic participant '{$generic}' remains: {$file}";
        }
    }

    if (preg_match('/class\s+[\p{L}-]+Service/u', $content, $classMatch)
        && preg_match('/[À-ỹ-]/u', $classMatch[0])) {
        $errors[] = "Invalid generated PHP class name '{$classMatch[0]}': {$file}";
    }

    $normalized = preg_replace('/\s+/', ' ', $section) ?? $section;
    $hash = hash('sha256', $normalized);
    $designs[$hash][] = $file;
}

foreach ($designs as $sameFiles) {
    if (count($sameFiles) > 1) {
        $errors[] = 'Duplicate design model: ' . implode(', ', array_map('basename', $sameFiles));
    }
}

if (count($files) !== 52) {
    $errors[] = 'Expected 52 exercise README files, found ' . count($files);
}

if ($errors !== []) {
    fwrite(STDERR, "FAIL exercise design model audit\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}

fwrite(STDOUT, "PASS exercise design model audit: 52 unique, topic-specific Mermaid models.\n");
