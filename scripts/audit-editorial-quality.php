<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$markdown = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

foreach ($iterator as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'md') {
        continue;
    }

    $path = $file->getPathname();
    if (str_contains($path, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR)) {
        continue;
    }

    $markdown[] = $path;
}

$issues = [];
$repeated = [];

foreach ($markdown as $path) {
    $content = file_get_contents($path);
    if ($content === false) {
        $issues[] = "Cannot read {$path}";
        continue;
    }

    $relative = ltrim(str_replace($root, '', $path), DIRECTORY_SEPARATOR);
    $wordCount = preg_match_all('/[\p{L}\p{N}_-]+/u', $content);

    $isSubstantive = preg_match('#^(docs|handbook|production|framework-integration|examples|exercises)/#', $relative) === 1
        && basename($relative) !== 'README.md';

    if ($isSubstantive && $wordCount < 100) {
        $issues[] = "Substantive article is too short ({$wordCount} words): {$relative}";
    }

    $inCode = false;
    foreach (preg_split('/\R/', $content) as $line) {
        $trimmed = trim($line);
        if (str_starts_with($trimmed, '```')) {
            $inCode = ! $inCode;
            continue;
        }

        if ($inCode || strlen($trimmed) < 100 || str_starts_with($trimmed, '#') || str_starts_with($trimmed, '|') || str_starts_with($trimmed, '-') || preg_match('/^\d+\./', $trimmed) === 1) {
            continue;
        }

        $normalized = preg_replace('/`[^`]+`/u', '`X`', $trimmed);
        $normalized = preg_replace('/\d+/u', 'N', (string) $normalized);
        if (trim((string) $normalized) !== '') {
            $repeated[$normalized][] = $relative;
        }
    }
}

foreach ($repeated as $line => $paths) {
    $uniquePaths = array_values(array_unique($paths));
    if (count($uniquePaths) >= 120) {
        $issues[] = sprintf(
            'Potential boilerplate repeated in %d files: %s | %s',
            count($uniquePaths),
            implode(', ', array_slice($uniquePaths, 0, 5)),
            substr($line, 0, 160),
        );
    }
}

if ($issues !== []) {
    fwrite(STDERR, "Editorial quality audit failed:\n- " . implode("\n- ", $issues) . "\n");
    exit(1);
}

echo sprintf("PASS editorial quality: %d Markdown files checked for depth and boilerplate\n", count($markdown));
