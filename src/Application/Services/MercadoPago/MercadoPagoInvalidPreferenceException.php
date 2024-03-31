<?php

declare(strict_types=1);

namespace App\Application\Services\MercadoPago;

use Exception;
class MercadoPagoInvalidPreferenceException extends Exception {
  public $message = "Algo deu errado! Por favor, revise os dados e tente novamente. Se o erro persistir, entre em contato com a equipe do suporte.";
}
