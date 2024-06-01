<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;
use App\Application\Actions\Action;
use App\Database\DatabaseInterface;
use App\Domain\ClientTicket\ClientTicketRepository;
use App\Domain\ParkingTicket\ParkingTicketRepository;
use App\Domain\Student\StudentRepository;
use App\Domain\Ticket\TicketRepository;
use Psr\Log\LoggerInterface;

abstract class ClientTicketAction extends Action {
    protected ClientTicketRepository $clientTicketRepository;
    protected TicketRepository $ticketRepository;
    protected StudentRepository $studentRepository;
    protected ParkingTicketRepository $parkingTicketRepository;

    public function __construct(
        LoggerInterface $logger, 
        DatabaseInterface $database, 
        ClientTicketRepository $clientTicketRepository, 
        TicketRepository $ticketRepository, 
        StudentRepository $studentRepository,
        ParkingTicketRepository $parkingTicketRepository
        ) {
        parent::__construct($logger, $database);
        $this->clientTicketRepository = $clientTicketRepository;
        $this->ticketRepository = $ticketRepository;
        $this->studentRepository = $studentRepository;
        $this->parkingTicketRepository = $parkingTicketRepository;
    }
}