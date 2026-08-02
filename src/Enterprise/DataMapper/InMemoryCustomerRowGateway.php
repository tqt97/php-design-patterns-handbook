<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\DataMapper;

final class InMemoryCustomerRowGateway implements CustomerRowGateway
{
    /** @var array<int,array{id:int,email:string,active:bool}> */
    private array $rows = [];

    public function find(int $id): ?array
    {
        return $this->rows[$id] ?? null;
    }

    public function persist(array $row): void
    {
        $this->rows[$row['id']] = $row;
    }
}
