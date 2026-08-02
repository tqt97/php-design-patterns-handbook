<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$failures = [];

// Handbook mental models.
$handbookFiles = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/handbook'));
$models = [];
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'md' || $file->getFilename() === 'README.md') {
        continue;
    }
    $content = file_get_contents($file->getPathname()) ?: '';
    $handbookFiles[] = $file->getPathname();
    if (!str_contains($content, '## Mental model') || !str_contains($content, '```mermaid')) {
        $failures[] = $file->getPathname() . ': missing Mental model or Mermaid diagram';
        continue;
    }
    $after = explode('## Mental model', $content, 2)[1];
    $section = preg_split('/\n## /', $after, 2)[0] ?? '';
    $normalized = preg_replace('/\s+/', ' ', trim($section));
    if (is_string($normalized)) {
        $models[$normalized][] = $file->getPathname();
    }
}
if (count($handbookFiles) !== 72) {
    $failures[] = 'handbook: expected 72 topic articles, found ' . count($handbookFiles);
}
foreach ($models as $files) {
    if (count($files) > 1) {
        $failures[] = 'handbook duplicate Mental model: ' . implode(', ', $files);
    }
}

// Interview depth.
foreach (['01-junior.md', '02-middle.md', '03-senior.md', '04-tech-lead.md'] as $name) {
    $path = $root . '/interviews/' . $name;
    $content = file_get_contents($path) ?: '';
    preg_match_all('/^##\s+\d+\./m', $content, $matches);
    if (count($matches[0]) < 15) {
        $failures[] = $path . ': expected at least 15 numbered questions';
    }
    if (substr_count($content, '**Trả lời chi tiết:**') < 15 || substr_count($content, '**Cách ghi điểm:**') < 15) {
        $failures[] = $path . ': answers or scoring guidance are incomplete';
    }
}
$scenarioContent = file_get_contents($root . '/interviews/05-live-design-scenarios.md') ?: '';
preg_match_all('/^##\s+\d+\./m', $scenarioContent, $scenarioMatches);
if (count($scenarioMatches[0]) < 8) {
    $failures[] = 'interviews: expected at least 8 live design scenarios';
}

// Training package completeness and depth.
$lessonDirs = glob($root . '/training/level-*/*', GLOB_ONLYDIR) ?: [];
if (count($lessonDirs) !== 15) {
    $failures[] = 'training: expected 15 lesson directories, found ' . count($lessonDirs);
}
$required = ['README.md', 'slides.md', 'speaker-notes.md', 'exercise.md', 'quiz.md', 'demo.php'];
foreach ($lessonDirs as $dir) {
    foreach ($required as $file) {
        if (!is_file($dir . '/' . $file)) {
            $failures[] = $dir . ': missing ' . $file;
        }
    }
    $readme = file_get_contents($dir . '/README.md') ?: '';
    foreach (['## Bối cảnh thuyết trình', '## Luồng hệ thống', '## Agenda 90 phút', '## Live coding', '## Tiêu chí hoàn thành'] as $heading) {
        if (!str_contains($readme, $heading)) {
            $failures[] = $dir . '/README.md: missing ' . $heading;
        }
    }
    if (!str_contains($readme, '```mermaid')) {
        $failures[] = $dir . '/README.md: missing Mermaid learning flow';
    }
    $slides = file_get_contents($dir . '/slides.md') ?: '';
    preg_match_all('/^## Slide\s+\d+/m', $slides, $slideMatches);
    if (count($slideMatches[0]) < 8) {
        $failures[] = $dir . '/slides.md: expected at least 8 slide sections';
    }
    $notes = file_get_contents($dir . '/speaker-notes.md') ?: '';
    if (!str_contains($notes, '## Câu hỏi dẫn dắt') || !str_contains($notes, '## Failure injection')) {
        $failures[] = $dir . '/speaker-notes.md: missing facilitation or failure-injection guidance';
    }
}

if ($failures !== []) {
    fwrite(STDERR, "FAIL learning experience audit:\n- " . implode("\n- ", $failures) . "\n");
    exit(1);
}

fwrite(STDOUT, "PASS learning experience audit: 72 handbook models, 60+ interview questions, 8 scenarios and 15 complete training lessons.\n");
