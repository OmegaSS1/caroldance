<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\DomainException\DomainRecordNotFoundException;

class UserInactiveException extends DomainRecordNotFoundException
{
    public $message = '[User (INACTIVE)] - O Usuario está inativo!';
} 
