<?php

declare(strict_types=1);

namespace App\Domain\Local;

use App\Domain\DomainException\DomainRecordNotFoundException;

class LocalAlreadyRegisterException extends DomainRecordNotFoundException
{
    public $message = '[Local (ALREADYREGISTER)] - O local já está cadastrado!';
}