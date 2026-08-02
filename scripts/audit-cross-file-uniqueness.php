<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$markdown = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'md') {
        continue;
    }
    $path = $file->getPathname();
    if (str_contains($path, DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR)) {
        continue;
    }
    $markdown[] = $path;
}

$mermaid = [];
$sections = [];
foreach ($markdown as $path) {
    $content = (string) file_get_contents($path);
    if (preg_match_all('/```mermaid\s*(.*?)```/s', $content, $matches)) {
        foreach ($matches[1] as $block) {
            $normalized = strtolower(trim((string) preg_replace('/\s+/', ' ', $block)));
            if (str_word_count($normalized) >= 6) {
                $mermaid[$normalized][] = $path;
            }
        }
    }

    $parts = preg_split('/^##\s+/m', $content) ?: [];
    array_shift($parts);
    foreach ($parts as $part) {
        $lines = preg_split('/\R/', $part) ?: [];
        $title = strtolower(trim((string) array_shift($lines)));
        $body = trim(implode("\n", $lines));
        $body = (string) preg_replace('/```.*?```/s', '', $body);
        $body = (string) preg_replace('/\[[^\]]+\]\([^\)]+\)/', 'link', $body);
        $body = (string) preg_replace('/`[^`]+`/', 'code', $body);
        $normalized = strtolower(trim((string) preg_replace('/\s+/', ' ', $body)));
        if (str_word_count($normalized) >= 25) {
            $sections[$title . "\0" . $normalized][] = $path;
        }
    }
}

$failures = [];
foreach ($mermaid as $block => $paths) {
    $paths = array_values(array_unique($paths));
    if (count($paths) > 1) {
        $failures[] = 'Mermaid block repeated in: ' . implode(', ', array_map(fn(string $p): string => str_replace($root . DIRECTORY_SEPARATOR, '', $p), $paths));
    }
}
foreach ($sections as $key => $paths) {
    $paths = array_values(array_unique($paths));
    if (count($paths) > 1) {
        [$title] = explode("\0", $key, 2);
        $failures[] = sprintf('Section "%s" has identical body in: %s', $title, implode(', ', array_map(fn(string $p): string => str_replace($root . DIRECTORY_SEPARATOR, '', $p), $paths)));
    }
}

if ($failures !== []) {
    fwrite(STDERR, "FAIL cross-file uniqueness audit:\n- " . implode("\n- ", $failures) . "\n");
    exit(1);
}

printf("PASS cross-file uniqueness audit: %d Markdown files; no repeated Mermaid blocks or identical 25+ word section bodies.\n", count($markdown));
