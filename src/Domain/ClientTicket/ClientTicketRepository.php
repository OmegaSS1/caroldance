<?php

declare(strict_types=1);

namespace App\Domain\ClientTicket;

interface ClientTicketRepository
{
    /**
     * @return ClientTicket[]
     */
    public function findAll(): array;

    /**
     * @return array
     */
    public function findAllByQuery(): array;

    /**
     * @param int $id
     * @return ClientTicket
     * @throws ClientTicketNotFoundException
     */
    public function findClientTicketById(int $id): ClientTicket;

    /**
     * @param int $id
     * @return mixed
     * @throws boolean
     */
    public function findClientTicketBySeatId(int $id);

    /**
     * @param string $period
     * @return int
     */
    public function findTotalClientTicketByPeriod(string $period);

    /**
     * @return int
     */
    public function findTotalClientTicketByParking();

    /**
     * @param array $period
     * @return array
     * @throws boolean
     */
    public function findClientTicketByPeriod(array $period): array;

    /**
     * @param string $cpf
     * @return mixed
     * @throws boolean
     */
    public function findClientTicketByCpf(string $cpf);

    /**
     * @param string $email
     * @return mixed
     * @throws boolean
     */
    public function findClientTicketByEmail(string $email);
    
    /**
     * @param int $id
     * @return array
     */
    public function findClientTicketByStudentId(int $id);
    
}
