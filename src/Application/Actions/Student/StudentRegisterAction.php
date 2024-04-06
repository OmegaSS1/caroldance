<?php

declare(strict_types=1);

namespace App\Application\Actions\Student;

use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class StudentRegisterAction extends StudentAction
{

    protected function action(): Response
    {

        $form = self::validateForm($this->post($this->request));

        $form['data_nascimento'] = $form['dataNascimento'];
        $form['servico_aluno_id'] = $form['servicoAlunoId'];
        unset($form['dataNascimento']);
        unset($form['servicoAlunoId']);

        $this->database->insert('aluno', $form);
        $this->database->commit();
        $this->logger->info("[Student - ID {$this->USER->data->id} IP ".IP."] - Aluno cadastrado com sucesso!", $form);

        return $this->respondWithData();
    }

    private function validateForm(array $form): array{
        $this->validKeysForm($form, ["nome", "sobrenome", "dataNascimento", "cpf", "servicoAlunoId"]);
        $form['cpf'] = $this->isCPF($form['cpf']);

        $years = $this->diffBetweenDatetimes(date('Y-m-d'), $form['dataNascimento'], 'y');
        [$year, $month, $day] = explode('-', $form['dataNascimento']);

        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            $this->logger->error("[Student - ID {$this->USER->data->id} IP ".IP."] - A data de nascimento informada está inválida!", $form);
            throw new Exception('A data de nascimento informada está inválida!');
        } 
        else if ($years < 18 or $years > 125) {
            $this->logger->error("[Student - ID {$this->USER->data->id} IP ".IP."] - O usuário precisa ter entre 18 e 125 anos!", $form);
            throw new Exception('O usuário precisa ter entre 18 e 125 anos!');
        }
        else if(!!$this->studentRepository->findUserByCpf($form['cpf'])){
            $this->logger->error("[Student - ID {$this->USER->data->id} IP ".IP."] - Aluno já cadastrado!", $form);
            throw new Exception('Aluno já cadastrado!');
        }

        return $form;
    }
}