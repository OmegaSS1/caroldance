<?php

declare(strict_types=1);

namespace App\Domain\Student;

use App\Domain\DomainException\DomainRecordNotFoundException;

class StudentNotFoundException extends DomainRecordNotFoundException
{
    public $message = '[Student (NOTFOUND)] - O Aluno não foi localizado!';
}