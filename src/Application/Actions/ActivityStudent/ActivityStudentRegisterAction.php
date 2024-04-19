<?php

declare(strict_types= 1);

namespace App\Application\Actions\ActivityStudent;

use App\Domain\DomainException\CustomDomainException;
use App\Domain\Local\LocalInactiveException;
use App\Domain\User\UserInactiveException;
use Psr\Http\Message\ResponseInterface as Response;
use DateTime;

class ActivityStudentRegisterAction extends ActivityStudentAction {

    protected function action(): Response{
        $form = self::validateForm($this->post($this->request));

        $form['nome']       = strtoupper($form['nome']);
        $form['h_inicial']  = $form['horarioInicial'];
        $form['h_final']    = $form['horarioFinal'];
        $form['usuario_id'] = $form['professor'];
        $form['local_id']   = $form['local'];

        unset($form['horarioInicial']);
        unset($form['horarioFinal']);
        unset($form['professor']);
        unset($form['local']);
        unset($form['semana']);

        $this->database->insert('atividade_aluno', $form);
        $this->database->commit();

        return $this->respondWithData();
    }

    private function validateForm(array $form): array {
        $this->validKeysForm($form, ['nome', 'horarioInicial', 'horarioFinal', 'professor', 'local']);
        
        $user     = $this->userRepository->findUserById($form['professor']);
        $local    = $this->localRepository->findLocalById($form['local']);
        
        $week     = ['Segunda', 'Terca', 'Quarta', 'Quinta', 'Sexta', 'Sabado', 'Domingo'];
        
        $hInicial = DateTime::createFromFormat('H:i', $form['horarioInicial']);
        $hFinal   = DateTime::createFromFormat('H:i', $form['horarioFinal']);

        if($user->getStatus() === 0){
            throw new UserInactiveException();
        }
        else if($local->getStatus() === 0){
            throw new LocalInactiveException();
        }
        else if(!$hInicial){
            throw new CustomDomainException('O Horario Inicial informado não é válido!');
        }
        else if(!$hFinal){
            throw new CustomDomainException('O Horario Final informado não é válido!');
        }
        else if($hInicial > $hFinal) {
            throw new CustomDomainException('O Horario Inicial não pode ser maior que o Horario Final!');
        }
        else if(empty($form['semana'])){
            throw new CustomDomainException('Selecione um ou mais dias da semana!');
        }

        foreach($form['semana'] as $dayWeek){
            $dayWeek = $this->format_string(explode('-', $dayWeek)[0]);
            if(!in_array($dayWeek, $week))
                throw new CustomDomainException("O dia da semana $dayWeek não é válido!");
            $form[strtolower($dayWeek)] = 1;
        }
        
        return $form;
    }
}