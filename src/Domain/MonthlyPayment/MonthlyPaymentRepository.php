<?php

declare(strict_types=1);

namespace App\Domain\MonthlyPayment;

interface MonthlyPaymentRepository
{
    /**
     * @return MonthlyPayment[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return MonthlyPayment
     * @throws MonthlyPaymentNotFoundException
     */
    public function findMonthlyPaymentById(int $id): MonthlyPayment;

}
