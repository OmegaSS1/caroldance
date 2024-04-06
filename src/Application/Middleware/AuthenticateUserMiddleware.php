<?php

namespace App\Application\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Application\Middleware\GenerateTokenJWTMiddleware;
use App\Application\Middleware\MiddlewareException\ExpiredTokenException;
use App\Application\Traits\Helper;
use App\Database\DatabaseInterface;
use Psr\Log\LoggerInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthenticateUserMiddleware implements Middleware {

  use Helper;
  private DatabaseInterface $database;
	private string $table = "usuario";
  private string $column = "id";

  protected LoggerInterface $logger;
  public function __construct(LoggerInterface $logger, DatabaseInterface $database){
    $this->database = $database;
    $this->logger = $logger;
  }
  public function process(Request $request, RequestHandler $handler): Response{

    $token = $request->getCookieParams()['Authorization'] ?? '';
    $decode_token = JWT::decode($token, new Key(ENV['SECRET_KEY_JWT'], 'HS256'));
    $user = $this->database->select('id', $this->table, "{$this->column} = '{$decode_token->data->id}'", "status = 1");

    if($decode_token->iss != IP) throw new ExpiredTokenException($request, $this->database);
    else if(empty($user)) throw new ExpiredTokenException($request, $this->database);

    $request = $request->withAttribute('USER', $decode_token);

    $tokenJWT = (new GenerateTokenJWTMiddleware($decode_token->iss, $decode_token->data->id))->getToken();
    $tokenCSRF = $this->generateTokenCSRF(IP);

    return $handler->handle($request)
    ->withHeader('Set-Cookie', "Authorization=$tokenJWT; Path=/; HttpOnly; Secure; SameSite=None")
    ->withAddedHeader('Set-Cookie', "X-Csrf-Token=$tokenCSRF; Path=/; HttpOnly; Secure; SameSite=None");
  }
}
