<?php

declare(strict_types=1);

namespace App\Domain\Student;

interface StudentRepository {
    
    /**
     * @return Student[]
     */
    public function findAll(): array;

    /**
     * @param string $cpf
     * @return mixed
     * @throws boolean
     */
    public function findUserByCpf(string $cpf);
}