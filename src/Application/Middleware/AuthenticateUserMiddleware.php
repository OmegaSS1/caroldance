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
  private string $table_token_csrf = "token_csrf";
	private string $table = "usuario";
  private string $column = "id";

  protected LoggerInterface $logger;
  public function __construct(LoggerInterface $logger, DatabaseInterface $database){
    $this->database = $database;
    $this->logger = $logger;
  }
  public function process(Request $request, RequestHandler $handler): Response{
    $token        = $request->getCookieParams()['Authorization'] ?? '';
    $decode_token = JWT::decode($token, new Key(ENV['SECRET_KEY_JWT'], 'HS256'));
    $user         = $this->database->select('id', $this->table, "{$this->column} = '{$decode_token->data->id}'", "status = 1");
    $logInfo      = [
      "User"     => $decode_token->data->id ?? "Usuário não logado",
      "Ip"       => IP,
      "Method"   => $request->getMethod(),
      "Route"    => $request->getUri()->getPath(),
      "Response" => "Sessão Expirada! Faça login novamente!"
    ];

    if($decode_token->iss != IP) {
      $this->database->delete($this->table_token_csrf, ["ip" => IP]);
      $this->database->commit();
      $request = $request->withHeader('Set-Cookie', '');

      $this->logger->warning(json_encode($logInfo, JSON_UNESCAPED_UNICODE), $user ?? $token ?? []);
      throw new ExpiredTokenException();
    }
    else if(empty($user)){
      $this->database->delete($this->table_token_csrf, ["ip" => IP]);
      $this->database->commit();
      $request = $request->withHeader('Set-Cookie', '');
      
      $this->logger->warning(json_encode($logInfo, JSON_UNESCAPED_UNICODE), $user ?? $token ?? []);
      throw new ExpiredTokenException();
    } 

    $request = $request->withAttribute('USER', $decode_token);

    $tokenJWT = (new GenerateTokenJWTMiddleware($decode_token->iss, $decode_token->data->id))->getToken();
    $tokenCSRF = (new GenerateTokenCSRFMiddleware($this->database))->getToken();

    

    return $handler->handle($request)
    ->withHeader('Set-Cookie', "Authorization=$tokenJWT; Path=/; HttpOnly; Secure; SameSite=None")
    ->withAddedHeader('Set-Cookie', "X-Csrf-Token=$tokenCSRF; Path=/; HttpOnly; Secure; SameSite=None");
  }
}
