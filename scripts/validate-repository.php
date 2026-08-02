<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$required = ['README.md', 'learning-path/README.md', 'playground/README.md', 'kata/README.md', 'exercises/README.md', 'production/payment-platform/README.md', 'handbook/ddd/README.md'];
foreach ($required as $file) {
    if (!is_file($root . '/' . $file)) {
        fwrite(STDERR, "Missing: {$file}\n"); exit(1);
    }
}
$counts = [
    'playgrounds' => count(glob($root . '/playground/*/index.php')),
    'katas' => count(glob($root . '/kata/*/solution.php')),
    'exercise_modules' => count(glob($root . '/exercises/module-*/SOLUTION.md')),
];
if ($counts['playgrounds'] < 100 || $counts['katas'] < 200 || $counts['exercise_modules'] * 6 < 300) {
    fwrite(STDERR, json_encode($counts) . "\n"); exit(1);
}
echo 'PASS repository validation ' . json_encode($counts) . PHP_EOL;
