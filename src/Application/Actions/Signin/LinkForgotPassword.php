<?php

declare(strict_types=1);

namespace App\Application\Actions\Signin;

use App\Application\Middleware\GenerateTokenJWTMiddleware;
use Psr\Http\Message\ResponseInterface as Response;

class LinkForgotPassword extends SigninAction{

  protected function action(): Response{

    $data = $this->post($this->request);
    $this->validKeysForm($data, ['email']);

    $token = new GenerateTokenJWTMiddleware(IP, $data['email']);

    $link  = IP . "/redefinirSenha?t=" . $token->getToken();
    $title = 'Save Money - Redefinição de senha';
    $body  = "Olá, parece que vocẽ esqueceu sua senha!<br>
    Clique no link abaixo para efetuar a troca de senha. <br>
    Link: <a href='$link'> Redefinir senha </a> <br><br>
    Não compartilhe sua senha. Ela assegura que o uso seja feito exclusivamente por você.<br>
    ";
    
    $this->database->update('usuario', ['tokenRedefinicaoSenha' => $token->getToken()], "email = '{$data['email']}'");
    $this->sendMail($title, $body, 'vini15_silva@hotmail.com');

    $this->database->commit();
    $this->logger->info("[LinkForgotPassword - IP ".IP."] Solicitação de troca de senha!", $data);

    return $this->respondWithData();

  }
}
