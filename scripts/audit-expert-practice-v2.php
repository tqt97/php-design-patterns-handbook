<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$required = [
    'docs/09-expert-practice/22-property-based-testing-workbook.md',
    'docs/09-expert-practice/23-mutation-testing-with-infection.md',
    'docs/09-expert-practice/24-distributed-bulkhead-and-bounded-waiting.md',
    'docs/09-expert-practice/25-deterministic-failure-injection.md',
    'docs/09-expert-practice/26-architecture-fitness-functions-in-ci.md',
    'docs/09-expert-practice/27-framework-source-tour-protocol.md',
    'docs/09-expert-practice/28-migration-rehearsal-dual-run.md',
    'docs/09-expert-practice/29-incident-packet-and-postmortem.md',
    'docs/09-expert-practice/30-design-evidence-graph.md',
    'framework-source-tours/laravel.md',
    'framework-source-tours/symfony.md',
    'incident-packets/templates/INCIDENT_PACKET.md',
    'evidence-graph/example.json',
];
$errors = [];
foreach ($required as $relative) {
    $path = $root . '/' . $relative;
    if (! is_file($path)) {
        $errors[] = "missing {$relative}";
        continue;
    }
    if (str_ends_with($relative, '.md')) {
        $content = file_get_contents($path) ?: '';
        if (str_word_count(strip_tags(preg_replace('/```.*?```/s', '', $content) ?? '')) < 120) {
            $errors[] = "{$relative}: insufficient explanatory depth";
        }
    }
}
$graph = json_decode((string) @file_get_contents($root . '/evidence-graph/example.json'), true);
if (! is_array($graph) || ! isset($graph['nodes'], $graph['edges'])) {
    $errors[] = 'evidence graph schema invalid';
}
if ($errors !== []) {
    fwrite(STDERR, "FAIL expert practice v2 audit\n" . implode("\n", $errors) . "\n");
    exit(1);
}
echo "PASS expert practice v2 audit\n";
