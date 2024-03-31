<?php

declare(strict_types=1);

namespace App\Application\Actions\Signin;

use App\Application\Middleware\GenerateTokenJWTMiddleware;
use App\Domain\User\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class Signin extends SigninAction {

  protected function action(): Response {
    $form = $this->post($this->request);

    $this->validKeysForm($form, ['cpf', 'senha']);
    $form['cpf'] = $this->isCPF($form['cpf']);

    $user = $this->userRepository->findUserByCpf($form['cpf']);
    
    if(!$user){
      $this->logger->error("[Signin - IP ".IP."] - Usuário não cadastrado!", $form);
      throw new UserNotFoundException('Usuário e/ou senha inválidos!');
    }
    else if(!password_verify($form['senha'], $user->getSenha())){
      $this->logger->error("[Signin - IP ".IP."] - Senha inválida!", $form);
      throw new UserNotFoundException('Usuário e/ou senha inválidos!');
    }

    $tokenJWT = (new GenerateTokenJWTMiddleware(IP, $form['cpf']))->getToken();
    $tokenCSRF = $this->generateTokenCSRF(IP);

    return $this->respondWithData()
    ->withHeader('Set-Cookie', "Authorization=$tokenJWT; Path=/; HttpOnly; Secure; SameSite=Strict")
    ->withAddedHeader('Set-Cookie', "X-Csrf-Token=$tokenCSRF; Path=/; HttpOnly; Secure; SameSite=Strict");
  }
}
