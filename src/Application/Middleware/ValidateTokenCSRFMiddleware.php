<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Middleware\MiddlewareException\InvalidTokenCSRFException;
use App\Application\Traits\Helper;
use App\Database\DatabaseInterface;
use Exception;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Log\LoggerInterface;

class ValidateTokenCSRFMiddleware implements Middleware {
  use Helper;
  protected DatabaseInterface $database;
	protected string $table_token_csrf = "token_csrf";
	protected string $table_black_list = "black_list";

  protected LoggerInterface $logger;


  public function __construct(DatabaseInterface $database, LoggerInterface $logger){
    $this->database = $database;
    $this->logger = $logger;
  }
  public function process(Request $request, RequestHandler $handler): Response 
  {
    if($request->getMethod() == 'POST'){
      $ip_request    = IP;
      $token_csrf    = $request->getCookieParams()['X-Csrf-Token'] ?? null;
      $form          = json_decode(file_get_contents('php://input'), true) ?? [];
      $user_request  = $this->database->select('*', $this->table_black_list, "ip = '$ip_request'");
      $database_token_csrf = $this->database->select('token', $this->table_token_csrf, "token = '$token_csrf'", "ip = '$ip_request'");
      
      if($user_request and $user_request[0]['bloqueio_permanente'] == 1){
        $this->logger->notice("[Middleware - IP $ip_request] Usuário Bloqueado! Entre em contato com o administrador.", $form);
        throw new Exception('Usuário Bloqueado! Entre em contato com o administrador.');
      }
      
      else if(!$database_token_csrf || !hash_equals($database_token_csrf[0]['token'], $token_csrf)){
        if(!$user_request){
          $route = $request->getUri()->getPath();
          $this->database->insert($this->table_black_list, [
            "ip" => $ip_request,
            "tentativa" => 1,
            "data_inclusao" => date('Y-m-d H:i:s'),
            "rota" => $route
          ]);

        }
        else {
          $user_request[0]["tentativa"] += 1;
          if($user_request[0]['tentativa'] >= 10)
            $this->database->update($this->table_black_list, ['tentativa' => $user_request[0]['tentativa'], "bloqueio_permanente" => 1, "observacao" => "Usuário suspeito"], "ip = '$ip_request'");
          else
            $this->database->update($this->table_black_list, ['tentativa' => $user_request[0]['tentativa']], "ip = '$ip_request'");
        }

        $this->database->commit();
        
        if(isset($user_request[0]["tentativa"]) and $user_request[0]["tentativa"] >= 10){
          $this->logger->info("[Middleware - IP $ip_request] Token CSRF Inválido! Usuário bloqueado!", $form);
          throw new InvalidTokenCSRFException($request, $this->database);
        }

        $this->logger->info("[Middleware - IP $ip_request] Token CSRF Inválido!", $form);
        throw new InvalidTokenCSRFException($request, $this->database);
      }
      
      if($user_request)
        $this->database->update($this->table_black_list, ['tentativa' => 0], "ip = '$ip_request'");

      $this->database->commit();
      return $handler->handle($request);
    }
    return $handler->handle($request);
  }
}
