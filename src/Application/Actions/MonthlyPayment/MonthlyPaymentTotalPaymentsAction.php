<?php

declare(strict_types=1);

namespace App\Application\Actions\MonthlyPayment;

use Psr\Http\Message\ResponseInterface as Response;
use DateTimeImmutable;

class MonthlyPaymentTotalPaymentsAction extends MonthlyPaymentAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $monthlyPayments = $this->monthlyPaymentRepository->findAll();

        foreach ($monthlyPayments as $monthlyPayment) {

            $name        = $this->studentRepository->findStudentById($monthlyPayment->getAlunoId())->getNome();
            $activity    = $this->activityStudentRepository->findActivityStudentById($this->studentRepository->findStudentById($monthlyPayment->getAlunoId())->getAtividadeAlunoId())->getNome();
            $month       = $monthlyPayment->getMes();
            $dateExpires = DateTimeImmutable::createFromFormat('Y-m-d', $monthlyPayment->getDhVencimento())->format('d/m/Y');
            $value       = number_format($this->activityStudentRepository->findActivityStudentById($this->studentRepository->findStudentById($monthlyPayment->getAlunoId())->getAtividadeAlunoId())->getValor(), 2, ',', '.');
            $status      = !$monthlyPayment->getStatusPagamento();

            $data[] = [
                "nome"       => $name,
                "atividade"  => $activity,
                "mes"        => $month,
                "vencimento" => $dateExpires,
                "valor"      => $value,
                "status"     => $status
            ];
        }

        return $this->respondWithData($data ?? []);
    }
}
