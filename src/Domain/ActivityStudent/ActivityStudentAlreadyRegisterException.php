<?php

declare(strict_types=1);

namespace App\Domain\ActivityStudent;

use App\Domain\DomainException\DomainRecordNotFoundException;

class ActivityStudentAlreadyRegisterException extends DomainRecordNotFoundException
{
    public $message = '[ActivityStudent (ALREADYREGISTER)] - Atividade já está cadastrada!';
}