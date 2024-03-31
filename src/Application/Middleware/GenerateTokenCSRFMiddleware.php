<?php

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;

class GenerateTokenCSRFMiddleware extends ActionMiddleware {

  protected string $table = 'token_csrf'; 
  protected function action(): Response {

    $token = $this->generateTokenCSRF(IP);

    return $this->respondWithData(['message' => 'OK'])->withHeader('Set-Cookie', "X-Csrf-Token=$token; Path=/; HttpOnly; Secure; SameSite=None");
  }
}
