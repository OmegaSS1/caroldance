<?php

declare(strict_types=1);

namespace App\Application\Middleware\MiddlewareException;

use App\Database\DatabaseInterface;
use Slim\Exception\HttpUnauthorizedException;

class InvalidTokenCSRFException extends HttpUnauthorizedException {
    public  function __construct($request, DatabaseInterface $database) {
    $database->delete('token_csrf', ["ip" => IP]);
    $database->commit();

    parent::__construct($request);
  }
}
