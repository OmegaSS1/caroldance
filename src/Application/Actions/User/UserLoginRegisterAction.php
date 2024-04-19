<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\User\UserAction;
use App\Domain\DomainException\CustomDomainException;
use Psr\Http\Message\ResponseInterface as Response;

class UserLoginRegisterAction extends UserAction
{

  protected function action(): Response
  {
    $form = self::validateForm($this->post($this->request));
    
    $form['telefone_whatsapp'] = preg_replace('/\D/', '', $form['whatsapp']);
    $form['data_nascimento']   = $form['dataNascimento'];
    $form['senha']             = password_hash(preg_replace("/\s+/", "", $form['senha']), PASSWORD_DEFAULT);
    $form['perfil_usuario_id'] = 1;

    unset($form['confirmarSenha']);
    unset($form['whatsapp']);
    unset($form['dataNascimento']);

    $this->database->insert('usuario', $form);
    $this->database->commit();

    return $this->respondWithData();
  }

  private function validateForm($form): array {
    $this->validKeysForm($form, ['nome', 'sobrenome', 'email', 'whatsapp', 'cpf', 'dataNascimento', 'senha', 'confirmarSenha']);
    $form['cpf'] = $this->isCPF($form['cpf']);
    $form['email'] = $this->isEmail($form['email']);

    $years = $this->diffBetweenDatetimes(date('Y-m-d'), $form['dataNascimento'], 'y');
    [$year, $month, $day] = explode('-', $form['dataNascimento']);

    if (!checkdate((int)$month, (int)$day, (int)$year)) {
      throw new CustomDomainException('A data de nascimento informada está inválida!');
    } 
    else if ($years < 18 or $years > 125) {
      throw new CustomDomainException('O usuário precisa ter entre 18 e 125 anos!');
    } 
    else if (!!$this->userRepository->findUserByCpf($form['cpf'])) {
      throw new CustomDomainException('Usuário já cadastrado!');
    } 
    else if ($form['senha'] != $form['confirmarSenha']) {
      throw new CustomDomainException('As senhas precisam ser iguais!');
    } 
    else if (strlen($form['whatsapp']) < 10 or strlen($form['whatsapp']) > 12) {
      throw new CustomDomainException('O telefone whatsapp está invalido!');
    }

    return $form;
  }
}
