<?php

namespace App\Application\Middleware;

use Firebase\JWT\JWT;

class GenerateTokenJWTMiddleware {

  private string $token;

  public function __construct(string $host, $user, int $minutes = 30){
    $payload = array(
      "iss" => $host,
      "aud" => $host,
      "iat" => time(),
      "exp" => time() + (60 * $minutes),
      "data" => [
        "id" => $user
      ]
    );

    $this->token = JWT::encode($payload, ENV['SECRET_KEY_JWT'], 'HS256');
		
  }

  public function getToken(){
    return $this->token;
  }
}
