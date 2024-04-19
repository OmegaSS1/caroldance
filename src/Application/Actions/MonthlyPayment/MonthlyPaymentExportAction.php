<?php

declare(strict_types=1);

namespace App\Application\Actions\MonthlyPayment;

use Psr\Http\Message\ResponseInterface as Response;
use DateTimeImmutable;

class MonthlyPaymentExportAction extends MonthlyPaymentAction
{

    protected function action(): Response
    {
        $monthlyPayments = $this->monthlyPaymentRepository->findAll();

        $data[]    = ['NOME', 'ATIVIDADE', 'MES', 'VENCIMENTO', 'VALOR', 'STATUS'];
        $monthName = [
            '1'  => 'Janeiro', 
            '2'  => 'Fevereiro', 
            '3'  => 'Março', 
            '4'  => 'Abril', 
            '5'  => 'Maio', 
            '6'  => 'Junho', 
            '7'  => 'Julho', 
            '8'  => 'Agosto', 
            '9'  => 'Setembro', 
            '10' => 'Outubro', 
            '11' => 'Novembro', 
            '12' => 'Dezembro'
        ];

        foreach ($monthlyPayments as $monthlyPayment) {
            $name        = $this->studentRepository->findStudentById($monthlyPayment->getAlunoId())->getNome();
            $activity    = $this->activityStudentRepository->findActivityStudentById($this->studentRepository->findStudentById($monthlyPayment->getAlunoId())->getAtividadeAlunoId())->getNome();
            $month       = $monthName[$monthlyPayment->getMes()];
            $dateExpires = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $monthlyPayment->getDhVencimento())->format('d/m/Y');
            $value       = number_format($this->activityStudentRepository->findActivityStudentById($this->studentRepository->findStudentById($monthlyPayment->getAlunoId())->getAtividadeAlunoId())->getValor(), 2, ',', '.');
            $status      = $monthlyPayment->getStatusPagamento();

            $data[] = [
                "nome"       => $name,
                "atividade"  => $activity,
                "mes"        => $month,
                "vencimento" => $dateExpires,
                "valor"      => $value,
                "status"     => $status
            ];
        }

        $filename = 'Relatorio de alunos ' . date('H:i:s d-m-Y');
        return $this->BoxSpout($data, $filename, $this->args['extension'], $this->respondWithData(), 'D');
    }
}