<?php

declare(strict_types=1);

namespace App\Application\Actions\Signin;

use App\Application\Middleware\GenerateTokenCSRFMiddleware;
use App\Application\Middleware\GenerateTokenJWTMiddleware;
use App\Domain\DomainException\CustomDomainException;
use App\Domain\User\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class SigninLoginAction extends SigninAction
{

  protected function action(): Response
  {
    $form = self::validateForm($this->post($this->request));

    $haveAluno = false;

    $user = $this->userRepository->findUserByCpf($form['cpf']);

    if ($user->getPerfilUsuarioId() != 1) {
      $haveAluno = true;
    }

    $tokenJWT = (new GenerateTokenJWTMiddleware(IP, $user->getId()))->getToken();
    $tokenCSRF = (new GenerateTokenCSRFMiddleware($this->database))->getToken();


    return $this->respondWithData(["responsavel" => $haveAluno])
    ->withHeader('Set-Cookie', "Authorization=$tokenJWT; Path=/; HttpOnly; Secure; SameSite=None")
    ->withAddedHeader('Set-Cookie', "X-Csrf-Token=$tokenCSRF; Path=/; HttpOnly; Secure; SameSite=None");

  }

  private function validateForm(array $form): array
  {
    $this->validKeysForm($form, ['cpf', 'senha']);
    $form['cpf'] = $this->isCPF($form['cpf']);

    $user = $this->userRepository->findUserByCpf($form['cpf']);

    if (!$user) {
      throw new CustomDomainException('Usuário e/ou senha inválidos!');
    } 
    else if (!password_verify($form['senha'], $user->getSenha())) {
      throw new CustomDomainException('Usuário e/ou senha inválidos!');
    } 
    else if (!$user->getStatus()) {
      throw new CustomDomainException('Usuário inativo! Em caso de duvidas, entre em contato com o suporte.');
    }

    return $form;
  }
}
