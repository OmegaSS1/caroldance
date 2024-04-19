<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\MonthlyPayment;

use App\Domain\MonthlyPayment\MonthlyPayment;
use App\Domain\MonthlyPayment\MonthlyPaymentNotFoundException;
use App\Domain\MonthlyPayment\MonthlyPaymentRepository;

use App\Database\DatabaseInterface;

class DataMonthlyPaymentRepository implements MonthlyPaymentRepository
{
    /**
     * @var MonthlyPayment[]
     */
    private array $monthlyPayment = [];

    /**
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database)
    {
        $data = $database->select('*', 'mensalidade');
        foreach ($data as $v) {
            $this->monthlyPayment[$v['id']] = new MonthlyPayment(
                (int)    $v['id'],
                (int)    $v['aluno_id'],
                (string) $v['mes'],
                (string) $v['dh_vencimento'],
                (string) $v['dh_pagamento'],
                (string) $v['status_pagamento'],
                (string) $v['observacoes'],
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
        return array_values($this->monthlyPayment);
    }

    /**
     * {@inheritdoc}
     */
    public function findMonthlyPaymentById(int $id): MonthlyPayment
    {
        if (!isset($this->monthlyPayment[$id])) {
            throw new MonthlyPaymentNotFoundException();
        }

        return $this->monthlyPayment[$id];
    }
}