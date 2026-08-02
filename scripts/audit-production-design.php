<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/production'));
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'md' || in_array($file->getFilename(), ['README.md', 'PRODUCTION_DESIGN_MATRIX.md'], true)) {
        continue;
    }
    $files[] = $file->getPathname();
}

$designBodies = [];
$errors = [];
foreach ($files as $path) {
    $content = file_get_contents($path);
    if ($content === false) {
        $errors[] = "Cannot read {$path}";
        continue;
    }
    if (!preg_match('/^## Thiết kế đề xuất\R(.*?)(?=^## |\z)/ms', $content, $match)) {
        $errors[] = "Missing design section: {$path}";
        continue;
    }
    $section = trim($match[1]);
    if (str_word_count(strip_tags(preg_replace('/```.*?```/s', '', $section) ?? '')) < 18) {
        $errors[] = "Design explanation too thin: {$path}";
    }
    if (!str_contains($section, '```mermaid')) {
        $errors[] = "Missing Mermaid design diagram: {$path}";
    }
    foreach (['Domain Module', 'Application Service', 'Port/Repository'] as $generic) {
        if (str_contains($section, $generic)) {
            $errors[] = "Generic participant '{$generic}' remains: {$path}";
        }
    }
    $normalized = preg_replace('/\s+/', ' ', strtolower($section)) ?? '';
    $designBodies[$normalized][] = $path;
}
foreach ($designBodies as $paths) {
    if (count($paths) > 1) {
        $errors[] = 'Duplicate production design section: ' . implode(', ', $paths);
    }
}

if ($errors !== []) {
    fwrite(STDERR, "FAIL production design audit\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}

echo 'PASS production design audit: ' . count($files) . " module files have unique, non-generic designs and diagrams.\n";
