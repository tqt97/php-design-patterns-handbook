<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$targets = [
    'benchmarks' => 300,
    'cheatsheets' => 250,
    'decisions' => 300,
    'docs' => 220,
    'examples' => 260,
    'exercises' => 400,
    'framework-integration' => 400,
    'handbook' => 350,
    'interviews' => 450,
    'kata' => 550,
    'labs' => 280,
    'learning-path' => 500,
    'playground' => 330,
    'production' => 350,
    'training' => 190,
];

$errors = [];
$checked = 0;
foreach ($targets as $directory => $minimumWords) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $directory));
    foreach ($iterator as $file) {
        if (! $file->isFile() || $file->getExtension() !== 'md') {
            continue;
        }
        $path = $file->getPathname();
        $relative = substr($path, strlen($root) + 1);
        $content = file_get_contents($path) ?: '';
        ++$checked;

        if (! preg_match('/^#\s+\S+/m', $content)) {
            $errors[] = "$relative: missing H1";
        }
        $plain = $content;
        $tokens = preg_split('/\s+/u', trim($plain), -1, PREG_SPLIT_NO_EMPTY);
        $words = is_array($tokens) ? count($tokens) : 0;
        if ($words < $minimumWords && basename($path) !== 'README.md') {
            $errors[] = "$relative: only $words words; expected >= $minimumWords";
        }
        if (substr_count($content, '```mermaid') !== preg_match_all('/```mermaid\s+.*?```/s', $content)) {
            $errors[] = "$relative: unclosed Mermaid fence";
        }
        if (str_contains($content, 'FirstImplementation') || str_contains($content, 'SecondImplementation')) {
            $errors[] = "$relative: generic design participant remains";
        }
    }
}

$phpIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src'));
foreach ($phpIterator as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $content = file_get_contents($file->getPathname()) ?: '';
    if (! str_contains($content, 'declare(strict_types=1);')) {
        $errors[] = substr($file->getPathname(), strlen($root) + 1) . ': missing strict_types';
    }
}

$required = [
    'src/Enterprise/Resilience/CircuitBreaker.php',
    'tests/Unit/Enterprise/Resilience/CircuitBreakerTest.php',
    'docs/09-expert-practice/19-circuit-breaker-operability.md',
    'SOURCE_ENTERPRISE_REVIEW.md',
];
foreach ($required as $path) {
    if (! is_file($root . '/' . $path)) {
        $errors[] = "$path: required enterprise evidence missing";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "FAIL enterprise release v2 audit:\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}

echo "PASS enterprise release v2 audit: {$checked} learning documents and source evidence checked.\n";
