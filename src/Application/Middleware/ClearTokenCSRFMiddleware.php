<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Database\DatabaseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ClearTokenCSRFMiddleware implements MiddlewareInterface{

  private DatabaseInterface $database;
  public function __construct(DatabaseInterface $database){
    $this->database = $database;
  }
  public function process(Request $request, RequestHandler $handler): Response{
    $this->database->delete('token_csrf', ["ip" => IP]);
    $this->database->commit();

    return $handler->handle($request);
  }
}
