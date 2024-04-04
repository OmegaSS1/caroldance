<?php

declare(strict_types=1);

namespace App\Application\Actions\Student;

use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class Register extends StudentAction
{

    protected function action(): Response
    {

        $form = $this->post($this->request);

        $this->validKeysForm($form, ["nome", "sobrenome", "data_nascimento", "cpf", "servicoAlunoId"]);
        $form['cpf'] = $this->isCPF($form['cpf']);

        $form['data_nascimento'] = $form['dataNascimento'];
        $years = $this->diffBetweenDatetimes(date('Y-m-d'), $form['data_nascimento'], 'y');

        if (!date_create($form['data_nascimento'])) {
            $this->logger->error("[Student - ID {$this->USER->id} IP ".IP."] - A data de nascimento informada está inválida!", $form);
            throw new Exception('A data de nascimento informada está inválida!');
        } else if ($years < 18 or $years > 125) {
            $this->logger->error("[Student - ID {$this->USER->id} IP ".IP."] - O usuário precisa ter entre 18 e 125 anos!", $form);
            throw new Exception('O usuário precisa ter entre 18 e 125 anos!');
        }

        $this->database->insert('aluno', $form);
        $this->database->commit();
        $this->logger->info("[Student - ID {$this->USER->id} IP ".IP."] - Aluno cadastrado com sucesso!", $form);

        return $this->respondWithData();
    }
}