<?php

declare(strict_types=1);

namespace App\Domain\ActivityStudent;

use App\Domain\DomainException\DomainRecordNotFoundException;

class ActivityStudentNotFoundException extends DomainRecordNotFoundException
{
    public $message = '[ActivityStudent (NOTFOUND)] - A atividade não foi localizada.';
}
