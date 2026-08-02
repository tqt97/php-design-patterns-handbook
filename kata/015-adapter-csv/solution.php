<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CsvPort { public function fetch(string $id): array; }
final class LegacyCsvSdk { public function getRecord(string $key): object { return (object) ['key'=>$key,'status'=>'OK']; } }
final class LegacyCsvAdapter implements CsvPort { public function __construct(private LegacyCsvSdk $sdk) {} public function fetch(string $id): array { $r=$this->sdk->getRecord($id); return ['id'=>$r->key,'active'=>$r->status==='OK']; } }
$record=(new LegacyCsvAdapter(new LegacyCsvSdk()))->fetch('customers.csv');
expect($record['id'] === 'customers.csv', 'mapped id'); expect($record['active'] === true, 'mapped status');
echo 'PASS kata 15' . PHP_EOL;
