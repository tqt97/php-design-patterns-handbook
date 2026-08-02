<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];

/** @param list<string> $files */
function checkFiles(array $files, int $minWords, bool $requireDiagram, string $label, array &$errors): void
{
    foreach ($files as $file) {
        $content = (string) file_get_contents($file);
        $plain = preg_replace('/```[\s\S]*?```/m', ' ', $content) ?? $content;
        preg_match_all('/[\p{L}\p{N}_-]+/u', $plain, $matches);
        $words = count($matches[0]);
        if ($words < $minWords) {
            $errors[] = sprintf('%s too shallow (%d < %d words): %s', $label, $words, $minWords, $file);
        }
        if ($requireDiagram && !str_contains($content, '```mermaid')) {
            $errors[] = sprintf('%s missing Mermaid diagram: %s', $label, $file);
        }
        if (!str_starts_with(trim($content), '# ')) {
            $errors[] = sprintf('%s missing H1: %s', $label, $file);
        }
    }
}

function globRecursive(string $base, string $pattern = '*.md'): array
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base));
    foreach ($iterator as $file) {
        if ($file->isFile() && fnmatch($pattern, $file->getFilename())) {
            $files[] = $file->getPathname();
        }
    }
    sort($files);
    return $files;
}

checkFiles(glob($root . '/benchmarks/*/README.md') ?: [], 300, true, 'benchmark', $errors);
checkFiles(glob($root . '/cheatsheets/*.md') ?: [], 220, false, 'cheatsheet', $errors);
checkFiles(array_values(array_filter(glob($root . '/decisions/*.md') ?: [], fn(string $f): bool => basename($f) !== 'README.md')), 280, false, 'ADR', $errors);
checkFiles(glob($root . '/examples/advanced/*/README.md') ?: [], 300, true, 'advanced example', $errors);
checkFiles(glob($root . '/framework-integration/*/*.md') ?: [], 350, true, 'framework integration', $errors);
checkFiles(glob($root . '/handbook/*/*.md') ?: [], 340, true, 'handbook chapter', $errors);
checkFiles(glob($root . '/interviews/*.md') ?: [], 380, false, 'interview guide', $errors);
checkFiles(glob($root . '/labs/*/*/README.md') ?: [], 280, true, 'lab', $errors);
checkFiles(glob($root . '/playground/flagship/*/README.md') ?: [], 280, true, 'flagship playground', $errors);
checkFiles(glob($root . '/production/*/README.md') ?: [], 340, true, 'production platform', $errors);
checkFiles(glob($root . '/training/*/*/README.md') ?: [], 300, true, 'training lesson', $errors);
checkFiles([$root . '/docs/09-expert-practice/20-enterprise-pattern-operability.md', $root . '/docs/09-expert-practice/21-pattern-adoption-evidence-pack.md'], 220, true, 'expert practice', $errors);

if ($errors !== []) {
    fwrite(STDERR, "FAIL enterprise practicality audit:\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}

printf("PASS enterprise practicality audit: depth, diagrams and H1 verified across enterprise learning assets.\n");
