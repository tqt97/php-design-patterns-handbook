<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];

$requirements = [
    'benchmarks' => ['## Mô hình phép đo', '## Ma trận workload khuyến nghị', '## Báo cáo kết quả'],
    'framework-integration' => ['## Phân tích production sâu hơn'],
    'production' => ['## Thiết kế đề xuất'],
];

foreach ($requirements as $directory => $headings) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $directory));
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'md' || in_array($file->getFilename(), ['README.md', 'PRODUCTION_DESIGN_MATRIX.md'], true)) {
            continue;
        }
        $content = file_get_contents($file->getPathname()) ?: '';
        foreach ($headings as $heading) {
            if (!str_contains($content, $heading)) {
                $errors[] = str_replace($root . '/', '', $file->getPathname()) . " missing {$heading}";
            }
        }
    }
}

$mustExist = [
    'SOURCE_ENTERPRISE_REVIEW.md',
    'DIRECTORY_QUALITY_MATRIX.md',
    'RELEASE_CHECKLIST.md',
];
foreach ($mustExist as $file) {
    if (!is_file($root . '/' . $file)) {
        $errors[] = "missing {$file}";
    }
}

$sourceReview = file_get_contents($root . '/SOURCE_ENTERPRISE_REVIEW.md') ?: '';
foreach (['## Dependency direction', '## Ma trận source → evidence', '## Checklist review từng file PHP'] as $heading) {
    if (!str_contains($sourceReview, $heading)) {
        $errors[] = "SOURCE_ENTERPRISE_REVIEW.md missing {$heading}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Enterprise content audit failed:\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}

echo "PASS enterprise content audit: benchmark, production, framework and source evidence verified.\n";
