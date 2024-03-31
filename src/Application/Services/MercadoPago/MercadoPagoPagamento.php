<?php

declare(strict_types=1);

namespace App\Application\Services\MercadoPago;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

class MercadoPagoPagamento extends Action{
  public function action(): Response {

    

    return $this->respondWithData();
  }
}
