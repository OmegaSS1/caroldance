<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\ParkingTicket;

use App\Domain\ParkingTicket\ParkingTicket;
use App\Domain\ParkingTicket\ParkingTicketNotFoundException;
use App\Domain\ParkingTicket\ParkingTicketRepository;

use App\Database\DatabaseInterface;

class DataParkingTicketRepository implements ParkingTicketRepository
{
    /**
     * @var ParkingTicket[]
     */
    private array $parkingTicket = [];

    /**
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database)
    {
        $data = $database->select('*', 'estacionamento_ingresso');
        foreach ($data as $v){
            $this->parkingTicket[$v['id']] = new ParkingTicket(
                (int)    $v['id'], 
                (int)    $v['aluno_id'], 
                (string) $v['periodo'],
                (string) $v['nome'],
                (string) $v['cpf'], 
                (string) $v['email'], 
                (int)    $v['valor'], 
                (string) $v['status_pagamento'], 
                (string) $v['dh_criacao'], 
                (string) $v['dh_atualizacao'] 
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return array_values($this->parkingTicket);
    }

    /**
     * {@inheritdoc}
     */
    public function findParkingTicketById(int $id): ParkingTicket
    {
        if (!isset($this->parkingTicket[$id])) {
            throw new ParkingTicketNotFoundException();
        }

        return $this->parkingTicket[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function findParkingTicketByStudentIdAndPeriod(int $alunoId, string $period){
        foreach($this->parkingTicket as $k => $parking){
            if($alunoId == $parking->getAlunoId() and $period == $parking->getPeriodo())
                return $this->parkingTicket[$k];
        }

        return [];
    }
}