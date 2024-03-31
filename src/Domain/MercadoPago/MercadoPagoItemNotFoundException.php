<?php

namespace App\Domain\MercadoPago;

use Exception;

class MercadoPagoItemNotFoundException  extends Exception {
  public $message = "Item não encontrado";
}