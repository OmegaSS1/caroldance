<?php

declare(strict_types=1);

namespace App\Application\Middleware\MiddlewareException;

use App\Database\DatabaseInterface;
use Slim\Exception\HttpUnauthorizedException;

class ExpiredTokenException extends HttpUnauthorizedException {
  public $message = "Sessão Expirada! Faça login novamente!";

  public  function __construct($request, DatabaseInterface $database) {
    $database->delete('token_csrf', ["ip" => IP]);
    $database->commit();
    $request = $request->withHeader('Set-Cookie', '');

    parent::__construct($request);
  }
}

