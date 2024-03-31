<?php

declare(strict_types=1);

namespace App\Application\Middleware\MiddlewareException;
use Slim\Exception\HttpBadRequestException;

class InvalidRecaptchaMiddleware extends HttpBadRequestException {
  public $message = "Token inválido";
}