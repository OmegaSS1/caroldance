<?php

declare(strict_types=1);

namespace App\Application\Services\MercadoPago;
use App\Application\Actions\Action;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface as Response;

class MercadoPagoCallback extends Action {

  protected LoggerInterface $logger;
  
  public function __construct(LoggerInterface $logger) {
    $this->logger = $logger;
  }

  protected function action(): Response {
    $data = $this->request->getQueryParams();

    // if(!empty($data)){
    //   $data = $data['data'];
    //   $preference_id = $data['preference_id'];
    //   unset($data['preference_id']);
    // }

    // $this->database->update("pagamentos", $data, "id");

    return $this->respondWithData($data);
  }

}
