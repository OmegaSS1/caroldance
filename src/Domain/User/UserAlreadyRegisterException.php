<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\DomainException\DomainRecordNotFoundException;

class UserAlreadyRegisterException extends DomainRecordNotFoundException
{
    public $message = '[User (ALREADYREGISTER)] - O Usuario já está cadastrado!';
}