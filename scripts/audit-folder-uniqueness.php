<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$groups = ['examples', 'exercises', 'framework-integration', 'handbook', 'production', 'playground', 'docs', 'cheatsheets', 'decisions'];
$errors = [];

/** @return array<int, true> */
function shingles(string $text): array
{
    $text = preg_replace('/```.*?```/s', ' ', $text) ?? $text;
    $text = preg_replace('/^#.*$/m', ' ', $text) ?? $text;
    $text = preg_replace('/`[^`]+`/', ' CODE ', $text) ?? $text;
    $text = preg_replace('/\d+/', 'N', strtolower($text)) ?? $text;
    preg_match_all('/[a-z\x{00C0}-\x{024F}]+/u', $text, $matches);
    $words = $matches[0] ?? [];
    $result = [];
    for ($i = 0, $max = count($words) - 7; $i <= $max; $i++) {
        $result[crc32(implode(' ', array_slice($words, $i, 8)))] = true;
    }
    return $result;
}

foreach ($groups as $group) {
    $docs = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $group));
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'md' || $file->getSize() < 700) {
            continue;
        }
        $set = shingles((string) file_get_contents($file->getPathname()));
        if (count($set) >= 30) {
            $docs[] = [$file->getPathname(), $set];
        }
    }

    for ($i = 0, $count = count($docs); $i < $count; $i++) {
        for ($j = $i + 1; $j < $count; $j++) {
            [$pathA, $a] = $docs[$i];
            [$pathB, $b] = $docs[$j];
            $ratio = count($a) / count($b);
            if ($ratio < 0.88 || $ratio > 1.14) {
                continue;
            }
            $intersection = count(array_intersect_key($a, $b));
            $union = count($a) + count($b) - $intersection;
            $score = $union > 0 ? $intersection / $union : 0.0;
            if ($score >= 0.82) {
                $errors[] = sprintf('%s: %.1f%% shingle overlap: %s <> %s', $group, $score * 100, str_replace($root . '/', '', $pathA), str_replace($root . '/', '', $pathB));
            }
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "FAIL folder uniqueness audit:\n- " . implode("\n- ", array_slice($errors, 0, 30)) . "\n");
    exit(1);
}

echo "PASS folder uniqueness audit: no topic files exceed the near-duplicate threshold.\n";
