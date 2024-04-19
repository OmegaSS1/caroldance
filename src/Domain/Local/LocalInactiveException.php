<?php

declare(strict_types=1);

namespace App\Domain\Local;

use App\Domain\DomainException\DomainRecordNotFoundException;

class LocalInactiveException extends DomainRecordNotFoundException
{
    public $message = '[Local (INACTIVE)] - O Local não está ativo!';
}
