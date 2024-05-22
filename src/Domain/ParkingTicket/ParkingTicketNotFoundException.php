<?php

declare(strict_types=1);

namespace App\Domain\ParkingTicket;

use App\Domain\DomainException\DomainRecordNotFoundException;

class ParkingTicketNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'A vaga de estacionamento não foi localizado!';
}
