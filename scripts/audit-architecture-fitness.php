<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src')) as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $path = $file->getPathname();
    $relative = str_replace($root . '/', '', $path);
    $content = file_get_contents($path) ?: '';
    if (! str_contains($content, 'declare(strict_types=1);')) {
        $errors[] = "{$relative}: missing strict_types";
    }
    if (str_starts_with($relative, 'src/Domain/') && preg_match('/use\s+DesignPatterns\\\\(Infrastructure|Framework)\\\\/', $content)) {
        $errors[] = "{$relative}: domain depends on infrastructure/framework";
    }
}
if ($errors !== []) {
    fwrite(STDERR, "FAIL architecture fitness\n" . implode("\n", $errors) . "\n");
    exit(1);
}
echo "PASS architecture fitness functions\n";
