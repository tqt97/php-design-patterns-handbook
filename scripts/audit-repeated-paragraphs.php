<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$paragraphs = [];
$fileCount = 0;

foreach ($iterator as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'md') {
        continue;
    }

    $fileCount++;
    $path = $file->getPathname();
    $content = file_get_contents($path);
    if ($content === false) {
        fwrite(STDERR, "Cannot read {$path}\n");
        exit(1);
    }

    $content = preg_replace('/```.*?```/s', '', $content) ?? $content;
    foreach (preg_split('/\R\s*\R/', $content) ?: [] as $paragraph) {
        $paragraph = trim(preg_replace('/\s+/u', ' ', $paragraph) ?? $paragraph);
        preg_match_all('/./us', $paragraph, $characters);
        if (count($characters[0]) < 160 || str_starts_with($paragraph, '#')) {
            continue;
        }

        $normalized = strtolower($paragraph);
        $normalized = preg_replace('/`[^`]+`/u', 'CODE', $normalized) ?? $normalized;
        $normalized = preg_replace('/\b\d+\b/u', 'N', $normalized) ?? $normalized;
        $paragraphs[$normalized][] = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
    }
}

$violations = [];
foreach ($paragraphs as $paragraph => $files) {
    $uniqueFiles = array_values(array_unique($files));
    if (count($uniqueFiles) >= 10) {
        $violations[] = [
            'count' => count($uniqueFiles),
            'paragraph' => substr($paragraph, 0, 180),
            'files' => array_slice($uniqueFiles, 0, 8),
        ];
    }
}

if ($violations !== []) {
    usort($violations, static fn (array $a, array $b): int => $b['count'] <=> $a['count']);
    fwrite(STDERR, "FAIL repeated long paragraphs detected:\n");
    foreach ($violations as $violation) {
        fwrite(STDERR, sprintf("- %d files: %s\n", $violation['count'], $violation['paragraph']));
        foreach ($violation['files'] as $path) {
            fwrite(STDERR, "  - {$path}\n");
        }
    }
    exit(1);
}

echo "PASS repeated paragraph audit: {$fileCount} Markdown files; no long paragraph repeated in 10+ files.\n";
