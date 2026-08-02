<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];

$wordCount = static function (string $path): int {
    $content = file_get_contents($path);
    if ($content === false) {
        return 0;
    }
    $content = preg_replace('/```.*?```/s', '', $content) ?? $content;
    preg_match_all('/[\p{L}\p{N}_-]+/u', $content, $matches);
    return count($matches[0]);
};

$requireDepth = static function (string $glob, int $minimum, bool $requireMermaid = false) use ($root, &$errors, $wordCount): void {
    foreach (glob($root . '/' . $glob) ?: [] as $path) {
        if (! is_file($path)) {
            continue;
        }
        $relative = substr($path, strlen($root) + 1);
        $content = file_get_contents($path) ?: '';
        $words = $wordCount($path);
        if ($words < $minimum) {
            $errors[] = "{$relative}: {$words} words; expected at least {$minimum}.";
        }
        if ($requireMermaid && ! str_contains($content, '```mermaid')) {
            $errors[] = "{$relative}: missing Mermaid diagram.";
        }
    }
};

$requireDepth('docs/01-creational/*.md', 220, true);
$requireDepth('docs/02-structural/*.md', 220, true);
$requireDepth('docs/03-behavioral/*.md', 220, true);
$requireDepth('training/level-*/*/README.md', 180, true);
$requireDepth('training/level-*/*/slides.md', 160, true);
$requireDepth('training/level-*/*/speaker-notes.md', 160, false);
$requireDepth('training/level-*/*/exercise.md', 140, false);
$requireDepth('training/level-*/*/quiz.md', 130, false);
$requireDepth('labs/*/*/README.md', 180, true);

if ($errors !== []) {
    fwrite(STDERR, "FAIL learning depth audit:\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}

fwrite(STDOUT, "PASS learning depth audit: core patterns, training and labs meet depth/diagram requirements.\n");
