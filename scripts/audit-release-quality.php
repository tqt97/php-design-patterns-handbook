<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];

$required = [
    'README.md' => 900,
    'CHANGELOG.md' => 500,
    'OVERVIEW.md' => 500,
    'MANIFEST.md' => 140,
    'REVIEW_REPORT.md' => 400,
    'DIRECTORY_QUALITY_MATRIX.md' => 300,
    'RELEASE_CHECKLIST.md' => 250,
    'cheatsheets/code-smell-to-pattern.md' => 500,
    'training/README.md' => 450,
    'production/README.md' => 550,
    'src/README.md' => 350,
];

foreach ($required as $file => $minWords) {
    $path = $root . '/' . $file;
    if (!is_file($path)) {
        $errors[] = "missing required file: {$file}";
        continue;
    }
    $text = (string) file_get_contents($path);
    $plain = preg_replace('/```.*?```/s', '', $text) ?? $text;
    preg_match_all('/[\p{L}\p{N}_-]+/u', $plain, $matches);
    $count = count($matches[0]);
    if ($count < $minWords) {
        $errors[] = "{$file} is too thin: {$count} words, expected >= {$minWords}";
    }
}

$changelog = (string) file_get_contents($root . '/CHANGELOG.md');
if (preg_match('/^##\s+(?:v?\d+\.\d+|\[?\d+\.\d+)/mi', $changelog)) {
    $errors[] = 'CHANGELOG.md must remain cumulative and must not use version headings.';
}

$readme = (string) file_get_contents($root . '/README.md');
foreach (['OVERVIEW.md', 'DIRECTORY_QUALITY_MATRIX.md', 'RELEASE_CHECKLIST.md', 'CHANGELOG.md'] as $link) {
    if (!str_contains($readme, $link)) {
        $errors[] = "README.md does not link to {$link}";
    }
}

$sourceReadme = (string) file_get_contents($root . '/src/README.md');
foreach (['Test', 'smoke', 'strict_types', 'Source Map'] as $needle) {
    if (!stripos($sourceReadme, $needle)) {
        $errors[] = "src/README.md is missing expected concept: {$needle}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Release quality audit failed:\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}

printf("PASS release quality audit: %d core files checked; cumulative changelog and navigation verified.\n", count($required));
