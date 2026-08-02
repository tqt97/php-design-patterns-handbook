<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$thresholds = [
    'benchmarks' => 220,
    'cheatsheets' => 220,
    'decisions' => 260,
    'docs' => 240,
    'examples' => 240,
    'exercises' => 300,
    'framework-integration' => 300,
    'handbook' => 300,
    'interviews' => 260,
    'kata' => 240,
    'labs' => 250,
    'learning-path' => 300,
    'playground' => 240,
    'production' => 320,
    'training' => 220,
];
$errors = [];
$checked = 0;

foreach ($thresholds as $folder => $minimum) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $folder));
    foreach ($iterator as $file) {
        if (! $file->isFile() || $file->getExtension() !== 'md') {
            continue;
        }
        $relative = substr($file->getPathname(), strlen($root) + 1);
        $content = file_get_contents($file->getPathname());
        if ($content === false) {
            continue;
        }
        $prose = preg_replace('/```.*?```/s', ' ', $content) ?? $content;
        $prose = preg_replace('/`[^`]+`/', ' ', $prose) ?? $prose;
        preg_match_all('/[\p{L}\p{N}_-]+/u', $prose, $words);
        $count = count($words[0]);
        ++$checked;

        $isAuxiliary = preg_match('#/(quiz|speaker-notes|slides|exercise|SOLUTION)\.md$#', $relative) === 1;
        $effectiveMinimum = $isAuxiliary ? max(180, $minimum - 80) : $minimum;
        if ($count < $effectiveMinimum) {
            $errors[] = "$relative: {$count} words, expected >= {$effectiveMinimum}";
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "FAIL folder enterprise depth audit:\n- " . implode("\n- ", array_slice($errors, 0, 100)) . "\n");
    exit(1);
}

echo "PASS folder enterprise depth audit: {$checked} learning files meet folder-specific prose thresholds.\n";
