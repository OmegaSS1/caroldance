<?php

declare(strict_types=1);

namespace App\Application\Actions\Signin;

use App\Application\Middleware\GenerateTokenJWTMiddleware;
use App\Domain\User\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class Signin extends SigninAction
{

  protected function action(): Response
  {
    $form = $this->validateForm($this->post($this->request));

    $haveAluno = false;

    $user = $this->userRepository->findUserByCpf($form['cpf']);

    if ($user->getPerfilUsuarioId() != 1) {
      $haveAluno = true;
    }

    $tokenJWT = (new GenerateTokenJWTMiddleware(IP, $user->getId()))->getToken();

    return $this->respondWithData(["responsavel" => $haveAluno])
      ->withHeader('Set-Cookie', "Authorization=$tokenJWT; Path=/; HttpOnly; Secure; SameSite=None");
  }

  private function validateForm(array $form): array
  {
    $this->validKeysForm($form, ['cpf', 'senha']);
    $form['cpf'] = $this->isCPF($form['cpf']);

    $user = $this->userRepository->findUserByCpf($form['cpf']);

    if (!$user) {
      $this->logger->error("[Signin - IP " . IP . "] - Usuário não cadastrado!", $form);
      throw new UserNotFoundException('Usuário e/ou senha inválidos!');
    } 
    else if (!password_verify($form['senha'], $user->getSenha())) {
      $this->logger->error("[Signin - IP " . IP . "] - Senha inválida!", $form);
      throw new UserNotFoundException('Usuário e/ou senha inválidos!');
    } 
    else if (!$user->getStatus()) {
      $this->logger->error("[Signin - IP " . IP . "] - Usuário inativo!", $form);
      throw new UserNotFoundException('Usuário inativo! Em caso de duvidas, entre em contato com o suporte.');
    }

    return $form;
  }
}
