<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$files = glob($root . '/handbook/*/*.md') ?: [];
$models = [];
$errors = [];
$forbidden = ['Context: ', 'Forces', 'Design decision', 'Verification', 'Operational outcome', 'Revisit trigger'];

foreach ($files as $file) {
    if (basename($file) === 'README.md') {
        continue;
    }
    $text = (string) file_get_contents($file);
    if (! preg_match('/^## Mental model\s*$(.*?)(?=^##\s|\z)/ms', $text, $match)) {
        $errors[] = str_replace($root . '/', '', $file) . ': missing Mental model section';
        continue;
    }
    $section = trim($match[1]);
    if (! str_contains($section, '```mermaid')) {
        $errors[] = str_replace($root . '/', '', $file) . ': missing Mermaid model';
    }
    foreach ($forbidden as $phrase) {
        if (str_contains($section, $phrase)) {
            $errors[] = str_replace($root . '/', '', $file) . ': generic mental-model phrase ' . $phrase;
        }
    }
    $normalized = strtolower(preg_replace('/\s+/', ' ', preg_replace('/`[^`]+`/', '`x`', $section) ?? '') ?? '');
    $models[$normalized][] = str_replace($root . '/', '', $file);
}

foreach ($models as $paths) {
    if (count($paths) > 1) {
        $errors[] = 'duplicate Mental model: ' . implode(', ', $paths);
    }
}

if ($errors !== []) {
    fwrite(STDERR, "FAIL handbook mental models:\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}

echo sprintf("PASS handbook mental model audit: %d topic-specific models.\n", count($models));
