<?php

declare(strict_types=1);
$root = dirname(__DIR__);
$forbidden = [
    'Điều cần bảo vệ không phải số lượng class mà là invariant:',
    'ADR cần so sánh pattern với baseline trực tiếp và nêu cleanup condition.',
    'Boundary của module nhận command/query theo ngôn ngữ nghiệp vụ',
    'Source of truth của **',
    'Hãy minh họa bằng một đoạn PHP ngắn, chỉ ra code smell ban đầu',
    'Khi conditional/direct call vẫn tốt hơn?',
];
$errors = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
foreach ($iterator as $file) {
    if (!$file->isFile() || strtolower($file->getExtension()) !== 'md') continue;
    $path = $file->getPathname();
    $text = file_get_contents($path) ?: '';
    foreach ($forbidden as $phrase) {
        if (str_contains($text, $phrase)) $errors[] = str_replace($root . DIRECTORY_SEPARATOR, '', $path) . ': ' . $phrase;
    }
}
if ($errors !== []) { fwrite(STDERR, "FAIL domain-specific content audit
" . implode("
", $errors) . "
"); exit(1); }
echo "PASS domain-specific content audit: no known generic editorial phrases.
";
