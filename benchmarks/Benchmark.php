<?php

declare(strict_types=1);

final class Benchmark
{
    /**
     * @param callable(): mixed $operation
     * @return array{median_ms: float, min_ms: float, max_ms: float, checksum: string}
     */
    public static function measure(callable $operation, int $iterations = 100_000, int $rounds = 7): array
    {
        for ($i = 0; $i < 3_000; $i++) {
            $operation();
        }

        $times = [];
        $checksum = '';
        for ($round = 0; $round < $rounds; $round++) {
            $start = hrtime(true);
            $last = null;
            for ($i = 0; $i < $iterations; $i++) {
                $last = $operation();
            }
            $times[] = (hrtime(true) - $start) / 1_000_000;
            $checksum = hash('sha256', serialize($last));
        }

        sort($times);
        return [
            'median_ms' => $times[intdiv(count($times), 2)],
            'min_ms' => $times[0],
            'max_ms' => $times[array_key_last($times)],
            'checksum' => substr($checksum, 0, 12),
        ];
    }

    /** @param array<string, array{median_ms: float, min_ms: float, max_ms: float, checksum: string}> $results */
    public static function report(string $title, array $results): void
    {
        echo "\n{$title}\n" . str_repeat('=', strlen($title)) . "\n";
        foreach ($results as $name => $result) {
            printf(
                "%-28s median=%9.3f ms  min=%9.3f  max=%9.3f  checksum=%s\n",
                $name,
                $result['median_ms'],
                $result['min_ms'],
                $result['max_ms'],
                $result['checksum'],
            );
        }
        echo "\nLưu ý: đây là micro-benchmark giáo dục; không dùng kết quả đơn lẻ để quyết định kiến trúc.\n";
    }
}
