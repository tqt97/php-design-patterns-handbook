<?php

declare(strict_types=1);

$roots = ['kata', 'exercises', 'handbook', 'production'];
$groups = [];

foreach ($roots as $root) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'md') {
            continue;
        }

        $content = (string) file_get_contents($file->getPathname());
        $lines = preg_split('/\R/', $content) ?: [];
        if (isset($lines[0]) && str_starts_with($lines[0], '#')) {
            array_shift($lines);
        }

        $body = strtolower(trim((string) preg_replace('/\s+/', ' ', implode("\n", $lines))));
        $hash = hash('sha256', $body);
        $groups[$hash][] = $file->getPathname();
    }
}

$duplicates = array_values(array_filter($groups, static fn (array $files): bool => count($files) > 1));
if ($duplicates !== []) {
    fwrite(STDERR, "Duplicate Markdown bodies detected:\n");
    foreach ($duplicates as $files) {
        fwrite(STDERR, '- ' . implode(', ', $files) . PHP_EOL);
    }
    exit(1);
}

echo "PASS content integrity: no duplicate Markdown bodies\n";
