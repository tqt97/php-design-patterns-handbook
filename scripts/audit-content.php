<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$markdown = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'md' && !str_contains($file->getPathname(), DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR)) {
        $markdown[] = $file->getPathname();
    }
}
sort($markdown);

$errors = [];
$bodies = [];
$genericParagraphs = [];
$stopWords = ['pattern', 'design', 'php', 'readme', 'module', 'platform', 'handbook', 'bai', 'tap', 'cho', 'cua', 'trong', 'va'];

foreach ($markdown as $path) {
    $relative = ltrim(str_replace($root, '', $path), DIRECTORY_SEPARATOR);
    $content = (string) file_get_contents($path);
    $lines = preg_split('/\R/', $content) ?: [];
    $firstHeading = null;
    foreach ($lines as $line) {
        if (preg_match('/^#\s+(.+)$/', trim($line), $matches) === 1) {
            $firstHeading = trim($matches[1]);
            break;
        }
    }
    if ($firstHeading === null) {
        $errors[] = "[heading] {$relative}: thiếu H1";
        continue;
    }

    $withoutHeading = preg_replace('/^#\s+.*$/m', '', $content, 1) ?? $content;
    $normalized = strtolower(trim((string) preg_replace('/\s+/', ' ', $withoutHeading)));
    if (strlen($normalized) >= 120) {
        $hash = hash('sha256', $normalized);
        $bodies[$hash][] = $relative;
    }

    if (preg_match_all('/\[[^\]]+\]\(([^)]+)\)/', $content, $matches) === 1 || !empty($matches[1])) {
        foreach ($matches[1] as $target) {
            if (preg_match('~^(https?://|mailto:|\#)~', $target) === 1) {
                continue;
            }
            $targetPath = explode('#', $target, 2)[0];
            if ($targetPath === '') {
                continue;
            }
            $resolved = realpath(dirname($path) . DIRECTORY_SEPARATOR . $targetPath);
            if ($resolved === false) {
                $errors[] = "[link] {$relative}: {$target}";
            }
        }
    }

    // Relevance is reviewed through topic-specific content tests and human review;
    // this script keeps objective checks for headings, links and duplication.

    $paragraphs = preg_split('/\R\s*\R/', $withoutHeading) ?: [];
    foreach ($paragraphs as $paragraph) {
        $plain = trim((string) preg_replace('/\s+/', ' ', strip_tags($paragraph)));
        if (strlen($plain) >= 140 && !str_starts_with($plain, '```')) {
            $genericParagraphs[hash('sha256', strtolower($plain))]['text'] = $plain;
            $genericParagraphs[hash('sha256', strtolower($plain))]['files'][] = $relative;
        }
    }
}

foreach ($bodies as $files) {
    if (count($files) > 1) {
        $errors[] = '[duplicate-body] ' . implode(', ', $files);
    }
}

foreach ($genericParagraphs as $item) {
    $files = array_values(array_unique($item['files']));
    if (count($files) >= 50) {
        $errors[] = sprintf('[boilerplate:%d] %s | %s', count($files), implode(', ', array_slice($files, 0, 5)), substr($item['text'], 0, 120));
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Content audit failed:\n" . implode("\n", $errors) . "\n");
    exit(1);
}

printf("PASS content audit: %d Markdown files, headings/links/duplicates checked\n", count($markdown));
