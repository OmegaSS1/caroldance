<?php

declare(strict_types=1);

namespace App\Application\Actions\Student;

use Psr\Http\Message\ResponseInterface as Response;
use DateTimeImmutable;

class StudentListAction extends StudentAction
{

    protected function action(): Response
    {
        $students = $this->studentRepository->findAll();

        foreach ($students as $student) {
            $nome           = ucfirst($student->getNome()) . ' ' . ucfirst($student->getSobrenome());
            $cpf            = substr($student->getCpf(), 0, 3) . '.' . substr($student->getCpf(), 3, 3) . '.' . substr($student->getCpf(), 6, 3) . '-' . substr($student->getCpf(), 9, 2);
            $dataNascimento = DateTimeImmutable::createFromFormat('Y-m-d', $student->getDataNascimento())->format('d/m/Y');
            $atividadeAluno = strtoupper($this->activityStudentRepository->findActivityStudentById($student->getAtividadeAlunoId())->getNome());
            $status         = !$student->getStatus() ? 'Inativo' : 'Ativo';

            $data[] = [
                "nome"           => $nome,
                "cpf"            => $cpf,
                "dataNascimento" => $dataNascimento,
                "atividadeAluno" => $atividadeAluno,
                "status"         => $status
            ];
        }

        return $this->respondWithData($data ?? []);
    }
}