<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$requiredIndexes = [
    'docs/README.md','docs/00-foundations/README.md','docs/01-creational/README.md','docs/02-structural/README.md',
    'docs/03-behavioral/README.md','docs/04-enterprise-patterns/README.md','docs/05-laravel-patterns/README.md',
    'docs/06-case-studies/README.md','docs/07-training/README.md','docs/08-interactive/README.md',
    'cheatsheets/README.md','decisions/README.md','examples/README.md','exercises/README.md',
    'framework-integration/README.md','handbook/README.md','interviews/README.md','kata/README.md','labs/README.md',
    'playground/README.md','production/README.md','training/README.md',
    'training/level-01-foundations/README.md','training/level-02-core-patterns/README.md',
    'training/level-03-enterprise/README.md','training/level-04-architecture/README.md','training/level-05-tech-lead/README.md',
];
$errors = [];
$count = 0;
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getFilename() === 'README.md') {
        ++$count;
        $relative = str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname());
        $content = file_get_contents($file->getPathname()) ?: '';
        if (!preg_match('/^#\s+\S+/m', $content)) {
            $errors[] = "$relative: thiếu H1";
        }
    }
}
foreach ($requiredIndexes as $relative) {
    $path = $root . DIRECTORY_SEPARATOR . $relative;
    if (!is_file($path)) { $errors[] = "$relative: thiếu file"; continue; }
    $content = file_get_contents($path) ?: '';
    $wordCount = str_word_count(strip_tags(preg_replace('/```.*?```/s', '', $content) ?? $content));
    if ($wordCount < 55) $errors[] = "$relative: index quá mỏng ($wordCount từ)";
    if (!preg_match('/\[[^\]]+\]\((?!https?:|mailto:|#)[^)]+\)/', $content)) $errors[] = "$relative: thiếu link điều hướng";
}
if ($errors !== []) {
    fwrite(STDERR, "README AUDIT FAILED\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}
printf("PASS README audit: %d README files; %d key indexes checked.\n", $count, count($requiredIndexes));
