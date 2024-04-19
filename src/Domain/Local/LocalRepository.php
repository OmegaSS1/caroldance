<?php

declare(strict_types=1);

namespace App\Domain\Local;

interface LocalRepository
{
    /**
     * @return Local[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return Local
     * @throws LocalNotFoundException
     */
    public function findLocalById(int $id): Local;

}
