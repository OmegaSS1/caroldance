<?php

declare(strict_types=1);

namespace App\Domain\MonthlyPayment;

use App\Domain\DomainException\DomainRecordNotFoundException;

class MonthlyPaymentNotFoundException extends DomainRecordNotFoundException
{
    public $message = '[MonthlyPayment (NOTFOUND)] - A mensalidade não foi localizada!';
}
