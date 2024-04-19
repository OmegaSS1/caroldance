<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Middleware\MiddlewareException\InvalidTokenCSRFException;
use App\Application\Traits\Helper;
use App\Database\DatabaseInterface;
use App\Domain\DomainException\CustomDomainException;
use Exception;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Log\LoggerInterface;

class ValidateTokenCSRFMiddleware implements Middleware
{
  use Helper;
  private DatabaseInterface $database;
  private string $table_token_csrf = "token_csrf";
  private string $table_black_list = "black_list";
  private LoggerInterface $logger;


  public function __construct(DatabaseInterface $database, LoggerInterface $logger)
  {
    $this->database = $database;
    $this->logger = $logger;
  }
  public function process(Request $request, RequestHandler $handler): Response
  {
    if ($request->getMethod() == 'POST') {
      $ip_request          = IP;
      $token_csrf          = $request->getCookieParams()['X-Csrf-Token'] ?? '';
      $user_request        = $this->database->select('*', $this->table_black_list, "ip = '$ip_request'");
      $database_token_csrf = $this->database->select('token', $this->table_token_csrf, "token = '$token_csrf'", "ip = '$ip_request'");
      $logInfo             = [
        "User"     => $request->getAttribute("USER")->data->id ?? "Usuário não logado",
        "Ip"       => $ip_request,
        "Method"   => $request->getMethod(),
        "Route"    => $request->getUri()->getPath(),
        "Response" => 'Usuário Bloqueado! Entre em contato com o administrador.'
      ];

      if ($user_request and $user_request[0]['bloqueio_permanente'] == 1) {
        $this->database->delete($this->table_token_csrf, ["ip" => $ip_request]);
        $this->database->commit();
        $request = $request->withHeader('Set-Cookie', '');

        $this->logger->warning(json_encode($logInfo, JSON_UNESCAPED_UNICODE), $user_request ?? []);
        throw new CustomDomainException('Usuário Bloqueado! Entre em contato com o administrador.');
      } 
      else if (!$database_token_csrf || !hash_equals($database_token_csrf[0]['token'], $token_csrf)) {
        if (!$user_request) {
          $route = $request->getUri()->getPath();
          $this->database->insert($this->table_black_list, [
            "ip" => $ip_request,
            "tentativa" => 1,
            "data_inclusao" => date('Y-m-d H:i:s'),
            "rota" => $route
          ]);

        } else {
          $user_request[0]["tentativa"] += 1;
          if ($user_request[0]['tentativa'] >= 10)
            $this->database->update($this->table_black_list, ['tentativa' => $user_request[0]['tentativa'], "bloqueio_permanente" => 1, "observacao" => "Usuário suspeito"], "ip = '$ip_request'");
          else
            $this->database->update($this->table_black_list, ['tentativa' => $user_request[0]['tentativa']], "ip = '$ip_request'");
        }

        $this->database->delete($this->table_token_csrf, ["ip" => $ip_request]);
        $this->database->commit();
        $request = $request->withHeader('Set-Cookie', '');
        $logInfo['Response'] = 'Token Inválido!';

        $this->logger->warning(json_encode($logInfo, JSON_UNESCAPED_UNICODE), $user_request ?? []);
        throw new InvalidTokenCSRFException();
      }

      if ($user_request)
        $this->database->update($this->table_black_list, ['tentativa' => 0], "ip = '$ip_request'");

      $this->database->commit();

      $response = $handler->handle($request);
      $this->database->delete($this->table_token_csrf, ["ip" => $ip_request]);
      $this->database->commit();

      return $response;
    }
    return $handler->handle($request);
  }
}
