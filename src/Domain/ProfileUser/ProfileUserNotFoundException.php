<?php

declare(strict_types=1);

namespace App\Domain\ProfileUser;

use App\Domain\DomainException\DomainRecordNotFoundException;

class ProfileUserNotFoundException extends DomainRecordNotFoundException
{
    public $message = '[ProfileUser (NOTFOUND)] - O perfil não foi localizado';
}
