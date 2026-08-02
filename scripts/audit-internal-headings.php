<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

foreach ($iterator as $file) {
    if (!$file->isFile() || strtolower($file->getExtension()) !== 'md') {
        continue;
    }
    $path = $file->getPathname();
    if (str_contains($path, DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR)) {
        continue;
    }
    $relative = ltrim(str_replace($root, '', $path), DIRECTORY_SEPARATOR);
    $headings = [];
    foreach (preg_split('/\R/', (string) file_get_contents($path)) ?: [] as $line) {
        if (!preg_match('/^##\s+(.+)$/u', trim($line), $m)) {
            continue;
        }
        $key = strtolower(trim($m[1]));
        $headings[$key] = ($headings[$key] ?? 0) + 1;
    }
    foreach ($headings as $heading => $count) {
        if ($count > 1) {
            $errors[] = "{$relative}: duplicate H2 heading '{$heading}' ({$count} occurrences)";
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Internal heading audit failed:\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}

fwrite(STDOUT, "PASS internal heading audit: no duplicate H2 headings in Markdown files.\n");
