<?php

declare(strict_types=1);

namespace App\Application\Services\MercadoPago;
use App\Application\Actions\Action;
use App\Application\Traits\Helper;
use App\Database\DatabaseInterface;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use MercadoPago\Payment;
use MercadoPago\Plan;
use MercadoPago\Subscription;
use MercadoPago\Invoice;

class MercadoPagoNotification extends Action {

  private Payment $payment;
  private Plan $plan;
  private Subscription $subscription;
  private Invoice $invoice;

  use Helper;
  public function __construct(LoggerInterface $logger, DatabaseInterface $database){
    parent::__construct($logger, $database);

    $this->payment      = new Payment();
    $this->plan         = new Plan();
    $this->subscription = new Subscription();
    $this->invoice      = new Invoice();
  }

  public function action(): Response {
    $data = $this->post($this->request);
    $result = "";
    switch($data['type']) {
      case "payment":
          $result = $this->payment->find_by_id($data["data"]["id"]);
          break;
      case "plan":
          $result = $this->plan->find_by_id($data["data"]["id"]);
          break;
      case "subscription":
          $result = $this->subscription->find_by_id($data["data"]["id"]);
          break;
      case "invoice":
          $result = $this->invoice->find_by_id($data["data"]["id"]);
          break;
      case "point_integration_wh":
          // $_POST contém as informações relacionadas à notificação.
          break;
  }

    return $this->respondWithData($result);
  }
}
