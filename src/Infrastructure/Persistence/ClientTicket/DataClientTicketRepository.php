<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\ClientTicket;

use App\Domain\ClientTicket\ClientTicket;
use App\Domain\ClientTicket\ClientTicketNotFoundException;
use App\Domain\ClientTicket\ClientTicketRepository;

use App\Database\DatabaseInterface;

class DataClientTicketRepository implements ClientTicketRepository
{
    /**
     * @var ClientTicket[]
     */
    private array $clientTicket = [];

    private DatabaseInterface $databaseInterface;
    /**
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database)
    {
        $this->databaseInterface = $database;
        $data = $database->select('*', 'cliente_ingresso');
        foreach ($data as $v){
            $this->clientTicket[$v['id']] = new ClientTicket(
                (int)    $v['id'], 
                (int)    $v['aluno_id'], 
                (string) $v['nome'], 
                (string) $v['cpf'], 
                (string) $v['email'], 
                (int)    $v['ingresso_id'], 
                (int)    $v['valor'],
                (string) $v['tipo'], 
                (string) $v['periodo'], 
                (string) $v['status_pagamento'], 
                (int)    $v['estacionamento'], 
                (string) $v['dh_criacao'], 
                (string) $v['dh_atualizacao'], 
                (int)    $v['status']
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return array_values($this->clientTicket);
    }

    public function findAllByQuery(): array {
        $sql = "
        SELECT 
            DATE_FORMAT(cli.dh_criacao, '%Y-%m-%d %H:%i') AS 'Data',
            cli.nome AS Nome,
            cli.cpf AS CPF,
            JSON_ARRAYAGG(i.assento) AS Assento,
            cli.email AS Email,
            sum(cli.valor) AS Valor,
            CONCAT(al.nome, ' ', al.sobrenome) AS Aluna,
            cli.status_pagamento AS Status,
            periodo AS Sessao,
            estacionamento AS Estacionamento
        FROM cliente_ingresso cli
        JOIN aluno al ON al.id = cli.aluno_id
        JOIN ingressos i ON i.id = cli.ingresso_id
        WHERE cli.status = 1
        GROUP BY Data, cli.nome, cli.email, al.nome, al.sobrenome, status_pagamento, periodo, cpf, estacionamento, aluno_id
        ORDER BY Data DESC";
        return $this->databaseInterface->runSelect($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function findClientTicketById(int $id): ClientTicket
    {
        if (!isset($this->clientTicket[$id])) {
            throw new ClientTicketNotFoundException();
        }

        return $this->clientTicket[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function findClientTicketBySeatId(int $id)
    {
        foreach($this->clientTicket as $v){
            if($v->getIngressoId() === $id)
                $data[] = $v;
        }

        return $data ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function findTotalClientTicketByPeriod(string $period): int
    {
        return (int) $this->databaseInterface->select('COUNT(*) as total', 'cliente_ingresso', "periodo = '$period'", "status = 1")[0]['total'];
    }

    /**
     * {@inheritdoc}
     */
    public function findTotalClientTicketByParking(string $period): int
    {
        $sql = "SELECT SUM(total.total_contagem) total_contagem FROM (
                    SELECT IFNULL(SUM(est.estacionamento), 0) AS total_contagem
                                    FROM (
                                    SELECT 
                                        estacionamento
                                    FROM cliente_ingresso ci 
                                    WHERE status = 1
                                    AND periodo = '$period'
                                    GROUP BY dh_atualizacao, email, cpf, aluno_id, nome
                                ) AS est
                    UNION
                    SELECT COUNT(*)
                    FROM estacionamento_ingresso
                    WHERE status_pagamento != 'Cancelado'
                    AND periodo = '$period'
                ) total FOR UPDATE";
        
        $v1 = $this->databaseInterface->runSelect($sql);

        return (int) $v1[0]['total_contagem'];
    }

    /**
     * {@inheritDoc}
     */
    public function findClientTicketByPeriod(array $period): array
    {
        foreach($this->clientTicket as $v){
            foreach($period as $p)
                if($v->getPeriodo() === $p)
                    $data[] = $v;
        }

        return array_values($data) ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public function findClientTicketByCpf(string $cpf)
    {
        $cpfArray = array_map(function($v){ return $v->getCpf(); }, $this->clientTicket);
        $key = array_search($cpf, $cpfArray, true);
        
        if ($key === false) {
            return false;
        }

        return $this->clientTicket[$key];
    }

    /**
     * {@inheritDoc}
     */
    public function findClientTicketByEmail(string $email)
    {
        $emailArray = array_map(function($v){ return $v->getEmail(); }, $this->clientTicket);
        $key = array_search($email, $emailArray, true);
        
        if ($key === false) {
            return false;
        }

        return $this->clientTicket[$key];
    }

    /**
     * {@inheritDoc}
     */
    public function findClientTicketByStudentId(int $id)
    {
        foreach($this->clientTicket as $v){
            if($v->getAlunoId() === $id)
                $data[] = $v;
        }

        return $data ?? [];
    }
}