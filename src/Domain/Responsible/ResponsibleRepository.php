<?php

declare(strict_types=1);

namespace App\Domain\Responsible;

interface ResponsibleRepository
{
    /**
     * @return Responsible[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return Responsible
     * @throws ResponsibleNotFoundException
     */
    public function findUserOfId(int $id): Responsible;

}
