<?php

declare(strict_types=1);

namespace App\Application\Actions\Signin;

use App\Application\Traits\Helper;
use App\Domain\User\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class SigninChangePasswordAction extends SigninAction
{
  use Helper;
  protected function action(): Response
  {
    $form = $this->validateForm($this->post($this->request));

    $hash = password_hash($form['senha'], PASSWORD_DEFAULT);

    $this->database->update('usuario', ['senha' => $hash], "email = {$form['email']}");

    $title = "Save Money - Confirmação de Alteração de Senha";
    $body =
      "Prezado(a),
      <br>Este e-mail é para informá-lo(a) de que a senha associada à sua conta [Detalhes da Conta/Usuário] foi alterada com sucesso. Esta alteração foi realizada em " . date('d/m/Y') . " às " . date('H:i:s') . ".
      <br>Se você iniciou esta alteração, não é necessário tomar nenhuma ação adicional. Recomendamos manter sua nova senha em um local seguro e não compartilhá-la com ninguém.
      <br>Se você não solicitou esta alteração de senha e acredita que sua conta possa estar comprometida, é crucial agir imediatamente para proteger sua conta. Por favor, siga estes passos:
      <br>1. Tente redefinir sua senha imediatamente usando o recurso de redefinição de senha em nosso site/app.
      <br>2. Entre em contato com nosso suporte ao cliente pelo e-mail [E-mail de Suporte] ou pelo telefone [Número de Telefone] para que possamos ajudar a garantir a segurança da sua conta.
      <br>3. Revise as atividades recentes da conta para quaisquer ações ou alterações que você não reconheça.
      <br>A segurança da sua conta é de extrema importância para nós. Se tiver alguma dúvida ou precisar de assistência adicional, não hesite em entrar em contato conosco.
      Atenciosamente,
      Equipe de Suporte Save Money";
    $this->sendMail($title, $body, $form['email']);
    $this->database->commit();
    $this->logger->warning("[ForgotPassword - IP " . IP . "] Senha alterada com sucesso!", $form);

    return $this->respondWithData();
  }

  private function validateForm(array $form): array
  {
    $form['token'] = $this->request->getQueryParams()['t'] ?? '';
    $this->validKeysForm($form, ['token', 'senha', 'confirmarSenha']);

    if ($form['senha'] != $form['confirmarSenha']) {
      $this->logger->notice("[ForgotPassword - ID {$this->USER->data->id} IP " . IP . "] As senha precisam ser iguais!", $form);
      throw new Exception('As senhas precisam ser iguais!');
    }

    $jwtDecodeToken = JWT::decode($form['token'], new Key(ENV['SECRET_KEY_JWT'], 'HS256'));
    $user = $this->userRepository->findUserByEmail($jwtDecodeToken->data->id);
    $form['email'] = $jwtDecodeToken->data->email;

    if (!$user) {
      $this->logger->info("[ForgotPassword - IP " . IP . "] Usuario não encontrado!", $form);
      throw new UserNotFoundException('Email não cadastrado!');
    } 
    else if (!$user->getStatus) {
      $this->logger->info("[ForgotPassword - IP " . IP . "] Seu usuário foi inativado, não é possivel efetuar a troca de senha!", $form);
      throw new Exception('Seu usuário foi inativado, não é possivel efetuar a troca de senha!');

    }

    return $form;
  }
}
