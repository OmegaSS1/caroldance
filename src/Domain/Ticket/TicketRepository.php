<?php

declare(strict_types=1);

namespace App\Domain\Ticket;

interface TicketRepository
{
    /**
     * @return Ticket[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return Ticket
     * @throws TicketNotFoundException
     */
    public function findTicketById(int $id): Ticket;

    /**
     * @param string $seat
     * @return mixed
     * @throws boolean
     */
    public function findTicketBySeat(string $seat);

}
