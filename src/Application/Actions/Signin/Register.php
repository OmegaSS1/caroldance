<?php

declare(strict_types=1);

namespace App\Application\Actions\Signin;

use App\Domain\User\UserAlreadyRegisterException;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class Register extends SigninAction {

  protected function action(): Response 
  {    
    $form = $this->post($this->request);

    $this->validKeysForm($form, ['nome', 'sobrenome', 'email', 'whatsapp', 'cpf', 'dataNascimento', 'senha', 'confirmarSenha']);    
    
    $form['telefone_whatsapp'] = preg_replace('/\D/', '', $form['whatsapp']);  
    $form['data_nascimento'] = $form['dataNascimento'];
    
    $form['cpf']   = $this->isCPF($form['cpf']);
    $form['email'] = $this->isEmail($form['email']);
  
    if($this->diffBetweenDatetimes(date('Y-m-d'), $form['data_nascimento'], 'y') < 18) {
      $this->logger->error("[Register - IP ".IP."] - O usuário precisa ter no mínimo 18 anos!", $form);
      throw new Exception('O usuário precisa ter no mínimo 18 anos!');
    }
    else if(!!$this->userRepository->findUserByCpf($form['cpf'])){
      $this->logger->error("[Register - IP ".IP."] - Usuário já cadastrado!", $form);
      throw new UserAlreadyRegisterException();
    }
    else if($form['senha'] != $form['confirmarSenha']){
      $this->logger->error("[Register - IP ".IP."] - As senhas precisam ser iguais!", $form);
      throw new Exception('As senhas precisam ser iguais!');
    }
    else if (strlen($form['telefone_whatsapp']) < 10 or strlen($form['telefone_whatsapp']) > 12){
      $this->logger->error("[Register - IP ".IP."] - Telefone whatsapp inválido!", $form);
      throw new Exception('O telefone whatsapp está invalido!');
    }

    $form['senha'] = password_hash(preg_replace("/\s+/", "", $form['senha']),  PASSWORD_DEFAULT);
    $form['perfil_usuario_id'] = 1;

    unset($form['confirmarSenha']);
    unset($form['whatsapp']);
    unset($form['dataNascimento']);

    $this->database->insert('usuario', $form);
    $this->database->commit();

    $this->logger->info("[Register - IP ".IP."] - Usuario cadastrado com sucesso!", $form);
    return $this->respondWithData();
  }
}
