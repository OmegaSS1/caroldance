<?php

declare(strict_types=1);

namespace App\Application\Actions\Student;

use Psr\Http\Message\ResponseInterface as Response;
use DateTimeImmutable;

class StudentExportAction extends StudentAction
{

    protected function action(): Response
    {
        $students = $this->studentRepository->findAll();

        $data[] = ['NOME', 'CPF', 'DATA DE NASCIMENTO', 'ATIVIDADE ALUNO', 'STATUS'];

        foreach ($students as $student) {
            $nome           = ucfirst($student->getNome()) . ' ' . ucfirst($student->getSobrenome());
            $cpf            = $student->getCpf();
            $dataNascimento = DateTimeImmutable::createFromFormat('Y-m-d', $student->getDataNascimento())->format('d/m/Y');
            $atividadeAluno = strtoupper($this->activityStudentRepository->findActivityStudentById($student->getAtividadeAlunoId())->getNome());
            $status         = !$student->getStatus() ? 'Inativo' : 'Ativo';

            $data[] = [
                "nome"           => $nome,
                "cpf"            => $cpf,
                "dataNascimento" => $dataNascimento,
                "atividadeAluno" => $atividadeAluno,
                "status"         => $status,
            ];
        }

        $filename = 'Relatorio de alunos ' . date('H:i:s d-m-Y');
        return $this->BoxSpout($data, $filename, $this->args['extension'], $this->respondWithData(), 'D');
    }
}