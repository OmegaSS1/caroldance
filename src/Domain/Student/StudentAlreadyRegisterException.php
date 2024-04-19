<?php

declare(strict_types=1);

namespace App\Domain\Student;

use App\Domain\DomainException\DomainRecordNotFoundException;

class StudentAlreadyRegisterException extends DomainRecordNotFoundException
{
    public $message = '[Student (ALREADYREGISTER)] - O aluno já está cadastrado!';
}