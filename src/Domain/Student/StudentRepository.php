<?php

declare(strict_types=1);

namespace App\Domain\Student;

interface StudentRepository
{

    /**
     * @return Student[]
     */
    public function findAll(): array;

    /**
     * @param string $cpf
     * @return mixed
     * @throws boolean
     */
    public function findStudentByCpf(string $cpf);

    /**
     * @param int $id
     * @return Student
     * @throws StudentNotFoundException
     */
    public function findStudentById(int $id): Student;
}