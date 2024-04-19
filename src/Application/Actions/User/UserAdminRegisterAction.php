<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\User\UserAction;
use App\Domain\DomainException\CustomDomainException;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class UserAdminRegisterAction extends UserAction
{

  protected function action(): Response
  {
    $form = self::validateForm($this->post($this->request));
    $password_gen = $this->genPass();

    $form['nome']              = ucfirst($form['nome']);
    $form['sobrenome']         = ucfirst($form['sobrenome']);
    $form['telefone_whatsapp'] = preg_replace('/\D/', '', $form['whatsapp']);
    $form['data_nascimento']   = $form['dataNascimento'];
    $form['perfil_usuario_id'] = $form['perfil'];
    $form['senha']             = $password_gen['hash'];

    unset($form['confirmarSenha']);
    unset($form['whatsapp']);
    unset($form['dataNascimento']);
    unset($form['perfil']);

    $this->database->insert('usuario', $form);

    $body = EMAIL_BODY_REGISTER . "Usuario: ***" . substr($form['cpf'], 3, 3) . "***** <br> Senha: {$password_gen['pass']} <br><br><br> Atenciosamente, <br><br> Estudio Carol Dance";
    $this->sendMail(EMAIL_TITLE_REGISTER, $body, ["viniciusbarbosa@prefeitura.sp.gov.br"]);

    $this->database->commit();

    return $this->respondWithData();
  }

  private function validateForm(array $form): array
  {
    $this->validKeysForm($form, ['nome', 'sobrenome', 'email', 'whatsapp', 'cpf', 'dataNascimento', 'perfil', 'senha', 'confirmarSenha']);
    $form['cpf']   = $this->isCPF($form['cpf']);
    $form['email'] = $this->isEmail($form['email']);

    $years = $this->diffBetweenDatetimes(date('Y-m-d'), $form['dataNascimento'], 'y');
    [$year, $month, $day] = explode('-', $form['dataNascimento']);

    if (!checkdate((int) $month, (int) $day, (int) $year)) {
      throw new CustomDomainException('A data de nascimento informada está inválida!');
    } 
    else if ($years < 18 or $years > 125) {
      throw new CustomDomainException('O usuário precisa ter entre 18 e 125 anos!');
    } 
    else if (!!$this->userRepository->findUserByCpf($form['cpf'])) {
      throw new CustomDomainException('Usuário já cadastrado!');
    } 
    else if (strlen($form['whatsapp']) < 10 or strlen($form['whatsapp']) > 12) {
      throw new CustomDomainException('O telefone whatsapp está invalido!');
    }

    return $form;
  }
}
