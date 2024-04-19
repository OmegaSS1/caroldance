<?php

declare(strict_types=1);

namespace App\Domain\Responsible;

use App\Domain\DomainException\DomainRecordNotFoundException;

class ResponsibleNotFoundException extends DomainRecordNotFoundException
{
    public $message = '[Responsible (NOTFOUND)] - O responsável não foi localizado';
}
