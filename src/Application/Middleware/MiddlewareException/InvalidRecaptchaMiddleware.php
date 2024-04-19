<?php

declare(strict_types=1);

namespace App\Application\Middleware\MiddlewareException;

use App\Domain\DomainException\DomainRecordNotFoundException;

class InvalidRecaptchaMiddleware extends DomainRecordNotFoundException {
  public $message = "Token Inválido!";
}