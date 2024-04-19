<?php

declare(strict_types=1);

namespace App\Application\Actions\Student;

use App\Domain\DomainException\CustomDomainException;
use Psr\Http\Message\ResponseInterface as Response;

use DateTimeImmutable;

class StudentRegisterAction extends StudentAction
{

    protected function action(): Response
    {

        $form = self::validateForm($this->post($this->request));

        $id = $this->database->insert('aluno', [
            'nome'               => ucfirst($form['nome']),
            'sobrenome'          => ucfirst($form['sobrenome']),
            'data_nascimento'    => $form['dataNascimento'],
            "cpf"                => $form["cpf"],
            "atividade_aluno_id" => $form["atividadeAlunoId"],
        ]);

        $monthNow = (int) date('m');
        $meses    = [
            '1'  => date('Y') . '-01-05',
            '2'  => date('Y') . '-02-05', 
            '3'  => date('Y') . '-03-05', 
            '4'  => date('Y') . '-04-05', 
            '5'  => date('Y') . '-05-05', 
            '6'  => date('Y') . '-06-05', 
            '7'  => date('Y') . '-07-05', 
            '8'  => date('Y') . '-08-05', 
            '9'  => date('Y') . '-09-05', 
            '10' => date('Y') . '-10-05', 
            '11' => date('Y') . '-11-05', 
            '12' => date('Y') . '-12-05'
        ];
        foreach($meses as $mes => $dia) {
            $month = (int) DateTimeImmutable::createFromFormat('Y-m-d', $dia)->format('m');
            if($month > $monthNow){
                $this->database->insert('mensalidade', [
                    "aluno_id"         => $id,
                    "mes"              => $mes,
                    "dh_vencimento"    => $dia,
                    "status_pagamento" => 'Pendente',
                ]);
            }
        }

        $this->database->commit();

        return $this->respondWithData();
    }

    private function validateForm(array $form): array{
        $this->validKeysForm($form, ["nome", "sobrenome", "dataNascimento", "cpf", "atividadeAlunoId"]);
        $form['cpf'] = $this->isCPF($form['cpf']);

        $years = $this->diffBetweenDatetimes(date('Y-m-d'), $form['dataNascimento'], 'y');
        [$year, $month, $day] = explode('-', $form['dataNascimento']);

        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            throw new CustomDomainException('A data de nascimento informada está inválida!');
        } 
        else if ($years > 125) {
            throw new CustomDomainException('O Aluno precisa ter menos de 125 anos!');
        }
        else if(!!$this->studentRepository->findStudentByCpf($form['cpf'])){
            throw new CustomDomainException('Aluno já cadastrado!');
        }

        $this->activityStudentRepository->findActivityStudentById($form['atividadeAlunoId']);

        return $form;
    }
}