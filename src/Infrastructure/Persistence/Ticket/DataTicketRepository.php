<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Ticket;

use App\Domain\Ticket\Ticket;
use App\Domain\Ticket\TicketNotFoundException;
use App\Domain\Ticket\TicketRepository;

use App\Database\DatabaseInterface;

class DataTicketRepository implements TicketRepository
{
    /**
     * @var Ticket[]
     */
    private array $tickets = [];

    /**
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database)
    {
        $data = $database->select('*', 'ingressos');
        foreach ($data as $v){
            $this->tickets[$v['id']] = new Ticket(
                (int)    $v['id'], 
                (string) $v['letra'], 
                (string) $v['assento'], 
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
        return array_values($this->tickets);
    }

    /**
     * {@inheritdoc}
     */
    public function findTicketById(int $id): Ticket
    {
        if (!isset($this->tickets[$id])) {
            throw new TicketNotFoundException();
        }

        return $this->tickets[$id];
    }

    /**
     * {@inheritDoc}
     */
    public function findTicketBySeat(string $seat)
    {
        $seatArray = array_map(function($v){ return $v->getAssento(); }, $this->tickets);
        $key = array_search($seat, $seatArray, true);
        
        if ($key === false) {
            return false;
        }

        return $this->tickets[$key];
    }
}