<?php

declare(strict_types=1);

namespace App\Application\Actions\Signin;

use App\Application\Middleware\GenerateTokenJWTMiddleware;
use App\Domain\User\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class LinkForgotPassword extends SigninAction
{

  protected function action(): Response
  {
    $form  = $this->validateForm($this->post($this->request));
    $token = (new GenerateTokenJWTMiddleware(IP, $form['email']))->getToken();

    $link = EMAIL_LINK_CHANGE_PASSWORD . "?t=$token";
    $body = EMAIL_BODY_FORGOT_PASSWORD . "Link: <a href=$link> Redefinir senha </a>";

    $this->database->update('usuario', ['token_redefinicao_senha' => $token], "email = '{$form['email']}'");
    $this->sendMail(EMAIL_TITLE_FORGOT_PASSWORD, $body, [$form['email']]);

    $this->database->commit();
    $this->logger->alert("[LinkForgotPassword - IP " . IP . "] Solicitação de troca de senha!", $form);

    return $this->respondWithData();
  }

  private function validateForm(array $form): array{
    $this->validKeysForm($form, ['email']);

    $form['email'] = $this->isEmail($form['email']);

    $user = $this->userRepository->findUserByEmail($form['email']);

    if(!$user){
      $this->logger->error("[Signin - IP " . IP . "] - Usuário não cadastrado!", $form);
      throw new UserNotFoundException('Usuário e/ou senha inválidos!');
    }
    else if(!$user->getStatus()){
      $this->logger->error("[Signin - IP " . IP . "] - Usuário inativo!", $form);
      throw new Exception('Usuário inativo! Em caso de duvidas, entre em contato com o suporte.');
    }

    return $form;
  }
}
