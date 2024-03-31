<?php

declare(strict_types=1);

namespace App\Application\Actions\Signin;

use App\Application\Traits\Helper;
use App\Domain\User\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ChangePassword extends SigninAction{
  use Helper;
  protected function action(): Response{

    $data = $this->post($this->request);
    $data['token'] = $this->args['token'] ?? '';
    $this->validKeysForm($data, ['token', 'senha', 'confirmarSenha']);

    if($data['senha'] != $data['confirmarSenha']){
      $this->logger->notice("[ForgotPassword - IP ".IP."] As senha precisam ser iguais!", $data);
      throw new Exception('As senhas precisam ser iguais!');
    }

    $jwtDecodeToken = JWT::decode($data['token'], new Key(ENV['SECRET_KEY_JWT'], 'HS256'));

    if(!$this->userRepository->findUserOfEmail($jwtDecodeToken->data->email)){
      $this->logger->info("[ForgotPassword - IP ".IP."] Usuario não encontrado!", $data);
      throw new UserNotFoundException('Email não cadastrado!');
    }

    $hash = password_hash($data['senha'], PASSWORD_DEFAULT);

    $this->database->update('usuario', ['senha' => $hash], "email = {$jwtDecodeToken->data->user}");

    $title = "Save Money - Confirmação de Alteração de Senha";
    $body  = 
    "Prezado(a),
      <br>Este e-mail é para informá-lo(a) de que a senha associada à sua conta [Detalhes da Conta/Usuário] foi alterada com sucesso. Esta alteração foi realizada em ".date('d/m/Y')." às ".date('H:i:s').".
      <br>Se você iniciou esta alteração, não é necessário tomar nenhuma ação adicional. Recomendamos manter sua nova senha em um local seguro e não compartilhá-la com ninguém.
      <br>Se você não solicitou esta alteração de senha e acredita que sua conta possa estar comprometida, é crucial agir imediatamente para proteger sua conta. Por favor, siga estes passos:
      <br>1. Tente redefinir sua senha imediatamente usando o recurso de redefinição de senha em nosso site/app.
      <br>2. Entre em contato com nosso suporte ao cliente pelo e-mail [E-mail de Suporte] ou pelo telefone [Número de Telefone] para que possamos ajudar a garantir a segurança da sua conta.
      <br>3. Revise as atividades recentes da conta para quaisquer ações ou alterações que você não reconheça.
      <br>A segurança da sua conta é de extrema importância para nós. Se tiver alguma dúvida ou precisar de assistência adicional, não hesite em entrar em contato conosco.
      Atenciosamente,
      Equipe de Suporte Save Money";
    $this->sendMail($title, $body, $data['email']);
    $this->database->commit();
    $this->logger->warning("[ForgotPassword - IP ".IP."] Senha alterada com sucesso!", $data);

    return $this->respondWithData();
  }
}
