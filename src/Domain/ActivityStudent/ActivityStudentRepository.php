<?php

declare(strict_types=1);

namespace App\Domain\ActivityStudent;

interface ActivityStudentRepository
{
    /**
     * @return ActivityStudent[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return ActivityStudent
     * @throws ActivityStudentNotFoundException
     */
    public function findActivityStudentById(int $id): ActivityStudent;
}
