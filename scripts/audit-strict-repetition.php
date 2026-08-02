<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'md' && ! str_contains($file->getPathname(), '/vendor/')) {
        $files[] = $file->getPathname();
    }
}

$occurrences = [];
$samples = [];
foreach ($files as $file) {
    $text = file_get_contents($file) ?: '';
    $text = preg_replace('/```.*?```/s', '', $text) ?? $text;
    foreach (preg_split('/\R\s*\R/', $text) ?: [] as $paragraph) {
        $paragraph = trim(preg_replace('/\s+/', ' ', $paragraph) ?? $paragraph);
        preg_match_all('/[\p{L}\p{N}_]+/u', $paragraph, $words);
        if (count($words[0]) < 35) {
            continue;
        }

        $normalized = strtolower($paragraph);
        $normalized = preg_replace('/`[^`]+`/u', '`X`', $normalized) ?? $normalized;
        $normalized = preg_replace('/\d+/u', 'N', $normalized) ?? $normalized;
        $occurrences[$normalized][$file] = true;
        $samples[$normalized] = $paragraph;
    }
}

$duplicates = [];
foreach ($occurrences as $normalized => $matchedFiles) {
    if (count($matchedFiles) >= 3) {
        $duplicates[] = [count($matchedFiles), $samples[$normalized], array_keys($matchedFiles)];
    }
}

if ($duplicates !== []) {
    usort($duplicates, static fn (array $a, array $b): int => $b[0] <=> $a[0]);
    fwrite(STDERR, "FAIL strict repetition audit:\n");
    foreach ($duplicates as [$count, $sample, $matchedFiles]) {
        fwrite(STDERR, "- repeated in {$count} files: ".substr($sample, 0, 180)."\n");
        foreach (array_slice($matchedFiles, 0, 8) as $matchedFile) {
            fwrite(STDERR, '  - '.str_replace($root.'/', '', $matchedFile)."\n");
        }
    }
    exit(1);
}

printf("PASS strict repetition audit: %d Markdown files; no 35+ word paragraph repeated in 3+ files.\n", count($files));
