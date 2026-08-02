<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\ActiveRecord;

final class NoteRecord
{
    /** @var array<int,array{id:int,body:string}> */
    private static array $rows = [];

    public function __construct(public readonly int $id, public string $body)
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Note ID must be positive.');
        }
        if (trim($body) === '') {
            throw new \InvalidArgumentException('Note body cannot be empty.');
        }
    }

    public function save(): void
    {
        self::$rows[$this->id] = ['id' => $this->id, 'body' => $this->body];
    }

    public static function find(int $id): ?self
    {
        $row = self::$rows[$id] ?? null;

        return $row === null ? null : new self($row['id'], $row['body']);
    }

    public static function reset(): void
    {
        self::$rows = [];
    }
}
