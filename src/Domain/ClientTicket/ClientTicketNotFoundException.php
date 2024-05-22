<?php

declare(strict_types=1);

namespace App\Domain\ClientTicket;

use App\Domain\DomainException\DomainRecordNotFoundException;

class ClientTicketNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'O Cliente não foi localizado!';
}
