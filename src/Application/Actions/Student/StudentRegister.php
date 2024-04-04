<?php

declare(strict_types=1);

namespace App\Application\Actions\Student;

use App\Domain\Student\StudentAlreadyRegisterException;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class StudentRegister extends StudentAction
{

    protected function action(): Response
    {

        $form = $this->post($this->request);

        $this->validKeysForm($form, ["nome", "sobrenome", "dataNascimento", "cpf", "servicoAlunoId"]);
        $form['cpf'] = $this->isCPF($form['cpf']);

        $form['data_nascimento'] = $form['dataNascimento'];
        $form['servico_aluno_id'] = $form['servicoAlunoId'];
        $years = $this->diffBetweenDatetimes(date('Y-m-d'), $form['data_nascimento'], 'y');
        unset($form['dataNascimento']);
        unset($form['servicoAlunoId']);
        unset($form['csrf']);

        if (!date_create($form['data_nascimento'])) {
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

        $this->database->insert('aluno', $form);
        $this->database->commit();
        $this->logger->info("[Student - ID {$this->USER->data->id} IP ".IP."] - Aluno cadastrado com sucesso!", $form);

        return $this->respondWithData();
    }
}