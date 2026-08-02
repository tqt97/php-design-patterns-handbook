<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

/** @var array<string, array<string, true>> $occurrences */
$occurrences = [];
$markdownCount = 0;

foreach ($iterator as $file) {
    if (!$file->isFile() || strtolower($file->getExtension()) !== 'md') {
        continue;
    }

    $path = $file->getPathname();
    $relative = ltrim(str_replace($root, '', $path), DIRECTORY_SEPARATOR);
    $content = file_get_contents($path);
    if ($content === false) {
        fwrite(STDERR, "Cannot read {$relative}\n");
        exit(1);
    }

    ++$markdownCount;
    $content = preg_replace('/```.*?```/s', ' ', $content) ?? $content;
    $paragraphs = preg_split('/\R\s*\R/', $content) ?: [];

    foreach ($paragraphs as $paragraph) {
        $paragraph = trim((string) preg_replace('/\s+/', ' ', $paragraph));
        if ($paragraph === '' || str_starts_with($paragraph, '#')) {
            continue;
        }

        $words = preg_split('/\s+/', $paragraph) ?: [];
        if (count($words) < 15) {
            continue;
        }

        $normalized = strtolower($paragraph);
        $normalized = preg_replace('/`[^`]+`/', 'CODE', $normalized) ?? $normalized;
        $normalized = preg_replace('/\d+/', 'N', $normalized) ?? $normalized;
        $occurrences[$normalized][$relative] = true;
    }
}

$duplicates = [];
foreach ($occurrences as $paragraph => $files) {
    if (count($files) >= 2) {
        $duplicates[$paragraph] = array_keys($files);
    }
}

if ($duplicates !== []) {
    fwrite(STDERR, "FAIL short duplication audit: repeated 15+ word paragraphs detected.\n");
    foreach ($duplicates as $paragraph => $files) {
        fwrite(STDERR, '- ' . implode(', ', $files) . "\n  {$paragraph}\n");
    }
    exit(1);
}

fwrite(STDOUT, "PASS short duplication audit: {$markdownCount} Markdown files; no normalized 15+ word paragraph repeated across files.\n");
