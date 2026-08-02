<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];
$checked = 0;
$genericDiagram = 0;
$translationAwareAllowlist = [
    'CONTRIBUTING.md',
    'templates/decision-record.md',
    'decisions/examples/007-example-quality-gate.md',
    'decisions/035-store-times-in-utc.md',
    'docs/00-foundations/07-antipattern-overengineering.md',
    'docs/08-interactive/13-choose-sync-vs-async.md',
    'framework-integration/laravel/03-events-vs-jobs.md',
    'framework-integration/symfony/06-testing-services.md',
    'cheatsheets/refactoring-workflow.md',
    'cheatsheets/testing-patterns.md',
    'cheatsheets/18-error-modeling-cheatsheet.md',
    'cheatsheets/26-naming-guide.md',
    'cheatsheets/17-reviewing-abstractions.md',
    'cheatsheets/21-concurrency-control.md',
    'cheatsheets/11-testing-patterns-quick-reference.md',
    'handbook/clean-architecture/07-boundaries.md',
    'handbook/clean-architecture/02-entities.md',
    'handbook/clean-architecture/06-presenters.md',
    'handbook/clean-architecture/03-use-cases.md',
    'handbook/refactoring/06-replace-conditionals.md',
    'handbook/microservices/08-deployment.md',
    'handbook/software-design/03-modularity.md',
    'handbook/software-design/06-immutability.md',
    'handbook/software-design/09-designing-for-deletion.md',
    'handbook/software-design/10-invariants-first.md',
    'handbook/software-design/05-error-modeling.md',
];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
foreach ($iterator as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'md') {
        continue;
    }

    $path = $file->getPathname();
    $relative = ltrim(str_replace($root, '', $path), DIRECTORY_SEPARATOR);
    $content = file_get_contents($path) ?: '';
    ++$checked;

    if (! preg_match('/^#\s+(.+)$/m', $content, $heading)) {
        $errors[] = "Missing H1: {$relative}";
        continue;
    }

    $stem = preg_replace('/^\d+[-_]?/', '', pathinfo($path, PATHINFO_FILENAME));
    if (! in_array($relative, $translationAwareAllowlist, true) && ! in_array(strtolower((string) $stem), ['readme', 'index', 'solution', 'slides', 'speaker-notes', 'exercise', 'quiz', 'answer'], true)) {
        $tokens = array_values(array_filter(preg_split('/[^a-z0-9]+/i', strtolower((string) $stem)) ?: [], static fn (string $v): bool => strlen($v) >= 4));
        $haystack = strtolower($heading[1] . ' ' . substr(strip_tags($content), 0, 3000));
        if ($tokens !== [] && ! array_filter($tokens, static fn (string $token): bool => str_contains($haystack, $token))) {
            $errors[] = "Possible filename/topic mismatch: {$relative} -> {$heading[1]}";
        }
    }

    if (str_contains($content, "class Client\n  class Abstraction\n  class ConcreteImplementation")) {
        ++$genericDiagram;
        $errors[] = "Generic diagram remains: {$relative}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, implode(PHP_EOL, $errors) . PHP_EOL);
    fwrite(STDERR, "FAIL semantic alignment: " . count($errors) . " issue(s) across {$checked} Markdown files.\n");
    exit(1);
}

echo "PASS semantic alignment: {$checked} Markdown files; no generic GoF diagrams or obvious filename/topic mismatches.\n";
