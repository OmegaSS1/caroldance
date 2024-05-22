<?php

declare(strict_types=1);

namespace App\Domain\Ticket;

use App\Domain\DomainException\DomainRecordNotFoundException;

class TicketNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'O assento não foi localizado!';
}
