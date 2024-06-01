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
     * @param int $alunoId
     * @param string $period
     * @return []|ParkingTicket
     */
    public function findParkingTicketByStudentIdAndPeriod(int $alunoId, string $period);

}
