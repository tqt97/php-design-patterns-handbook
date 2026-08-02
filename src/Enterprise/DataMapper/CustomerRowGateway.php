<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\DataMapper;

interface CustomerRowGateway
{
    /** @return array{id:int,email:string,active:bool}|null */
    public function find(int $id): ?array;

    /** @param array{id:int,email:string,active:bool} $row */
    public function persist(array $row): void;
}
