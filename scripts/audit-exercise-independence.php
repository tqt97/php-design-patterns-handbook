<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$files = glob($root . '/exercises/module-*/*.md') ?: [];
sort($files);
$errors = [];
$vectors = [];

foreach ($files as $file) {
    $content = (string) file_get_contents($file);
    $relative = ltrim(str_replace($root, '', $file), DIRECTORY_SEPARATOR);

    foreach (['##', '```mermaid'] as $required) {
        if (!str_contains($content, $required)) {
            $errors[] = "{$relative}: thiếu {$required}";
        }
    }

    if (basename($file) === 'README.md') {
        foreach (['## Câu chuyện nghiệp vụ', '## Nhiệm vụ', '## Test bắt buộc', '## Câu hỏi design review'] as $section) {
            if (!str_contains($content, $section)) {
                $errors[] = "{$relative}: thiếu {$section}";
            }
        }
    } else {
        foreach (['## Các bước refactor', '## Test suite tối thiểu', '## Failure walkthrough', '## Trade-off và phương án thay thế'] as $section) {
            if (!str_contains($content, $section)) {
                $errors[] = "{$relative}: thiếu {$section}";
            }
        }
    }

    $normalized = preg_replace('/```.*?```/s', ' ', strtolower($content)) ?? $content;
    $normalized = preg_replace('/`[^`]+`/', ' token ', $normalized) ?? $normalized;
    $normalized = preg_replace('/\d+/', ' n ', $normalized) ?? $normalized;
    $normalized = preg_replace('/[^\pL\pN\s]+/u', ' ', $normalized) ?? $normalized;
    $words = array_values(array_filter(preg_split('/\s+/u', $normalized) ?: [], static fn (string $word): bool => strlen($word) > 3));
    $vectors[$relative] = array_count_values($words);
}

$names = array_keys($vectors);
for ($i = 0, $count = count($names); $i < $count; ++$i) {
    for ($j = $i + 1; $j < $count; ++$j) {
        $aName = $names[$i];
        $bName = $names[$j];
        $a = $vectors[$aName];
        $b = $vectors[$bName];
        $dot = 0.0;
        foreach ($a as $word => $frequency) {
            $dot += $frequency * ($b[$word] ?? 0);
        }
        $normA = sqrt(array_sum(array_map(static fn (int $v): int => $v * $v, $a)));
        $normB = sqrt(array_sum(array_map(static fn (int $v): int => $v * $v, $b)));
        $similarity = ($normA > 0 && $normB > 0) ? $dot / ($normA * $normB) : 0.0;
        if ($similarity >= 0.90) {
            $errors[] = sprintf('near-duplicate %.3f: %s <> %s', $similarity, $aName, $bName);
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Exercise independence audit failed:\n" . implode("\n", $errors) . "\n");
    exit(1);
}

printf("PASS exercise independence audit: %d files; required sections present; no pair >= 0.90 similarity.\n", count($files));
