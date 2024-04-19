<?php

declare(strict_types=1);

namespace App\Domain\Local;

use App\Domain\DomainException\DomainRecordNotFoundException;

class LocalNotFoundException extends DomainRecordNotFoundException
{
    public $message = '[Local (NOTFOUND)] - O local não foi localizado!';
}
