<?php

declare(strict_types=1);

namespace App\Domain\ProfileUser;

interface ProfileUserRepository
{
    /**
     * @return ProfileUser[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return ProfileUser
     * @throws ProfileUserNotFoundException
     */
    public function findProfileUserById(int $id): ProfileUser;

}
