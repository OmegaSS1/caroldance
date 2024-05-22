<?php

declare(strict_types=1);

namespace App\Domain\ParkingTicket;

interface ParkingTicketRepository
{
    /**
     * @return ParkingTicket[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return ParkingTicket
     * @throws ParkingTicketNotFoundException
     */
    public function findParkingTicketById(int $id): ParkingTicket;

    /**
     * @param string $ticket
     * @return mixed
     * @throws boolean
     */
    public function findParkingTicketByParkingTicket(string $ticket);

}
