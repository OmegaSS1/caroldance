<?php
declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Middleware\ActionMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class ValidateTokenJWTMiddleware extends ActionMiddleware {

  public function action(): Response{
    $token = $this->args['token'] ?? '';
    JWT::decode($token, new Key(ENV['SECRET_KEY_JWT'], 'HS256'));

    return $this->respondWithData();
  }
}
