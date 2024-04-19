<?php

declare(strict_types=1);

namespace App\Application\Middleware\MiddlewareException;

use App\Domain\DomainException\DomainRecordNotFoundException;

class ExpiredTokenException extends DomainRecordNotFoundException {
  public $message = "Sessão Expirada! Faça login novamente!";
}
