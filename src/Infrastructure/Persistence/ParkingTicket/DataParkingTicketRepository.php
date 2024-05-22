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
        $data = $database->select('*', 'ingressos');
        foreach ($data as $v){
            $this->parkingTicket[] = new ParkingTicket(
                (string) $v['assento'], 
                (int)    $v['cliente_ingresso_id'], 
                (int)    $v['valor'],
                (string) $v['tipo'],
                (string) $v['periodo'], 
                (string) $v['status_pagamento'], 
                (string) $v['dh_criacao'], 
                (string) $v['dh_atualizacao'], 
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
     * {@inheritDoc}
     */
    public function findParkingTicketByParkingTicket(string $seat)
    {
        $parkingTicketArray = array_map(function($v){ return $v->getAssento(); }, $this->parkingTicket);
        $key = array_search($seat, $parkingTicketArray, true);
        
        if ($key === false) {
            return false;
        }

        return $this->parkingTicket[$key];
    }
}