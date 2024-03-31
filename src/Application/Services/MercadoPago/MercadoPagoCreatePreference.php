<?php 

declare(strict_types=1);

namespace App\Application\Services\MercadoPago;

use App\Application\Actions\Action;
use App\Database\DatabaseInterface;
use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Ramsey\Uuid\Uuid;

class MercadoPagoCreatePreference extends Action{

  private Preference $preference;
  private Item $item;

  public function  __construct(LoggerInterface $logger, DatabaseInterface $database){
    parent::__construct($logger,$database);
    SDK::setAccessToken(ENV['MERCADOPAGO_TOKEN']);
    $this->item = new Item();
    $this->preference = new Preference();
  }

  protected function action(): Response {
    $id   = (int) $this->resolveArg('id');
    $item = $this->database->select('category_id, currency_id, description, title, unit_price', 'mercadopago_item', "id = $id");

    

    $this->item->title       = 'Ingresso';
    $this->item->description = 'Cada ingresso é válido somente para uma pessoa!'; 
    $this->item->quantity    = '1';
    $this->item->current_id  = "BRL";
    $this->item->unit_price = (float) 3;

    $this->preference->items = array($this->item);
    $this->preference->payment_methods = array(
      "excluded_payment_methods" => array(
        array("id" => "amex")
      ),
      "excluded_payment_types" => array(
        array("id" => "debit_card"),
        array("id" => "atm"),
        array("id" => "crypto_transfer"),
        array("id" => "prepaid_card"),
        array("id" => "voucher_card")
      ),
      "default_payment_method_id" => "pix",
      "installments" => 3,
      "default_installments" => 1
    );

    define('UUID', Uuid::uuid4()->toString());
    $this->preference->external_reference = UUID;
    $this->preference->back_urls = array(
        "success" => "https://h-simcepi.smsprefeiturasp.com.br/appteste/services/mercadopago/callback",
        "failure" => "https://h-simcepi.smsprefeiturasp.com.br/appteste/services/mercadopago/callback", 
        "pending" => "https://h-simcepi.smsprefeiturasp.com.br/appteste/services/mercadopago/callback"
    );
    $this->preference->notification_url = 'https://h-simcepi.smsprefeiturasp.com.br/appteste/services/mercadopago/notification';
    // $this->preference->auto_return = 'approved';
    $this->preference->save();

    if($this->preference->id == null)
      throw new MercadoPagoInvalidPreferenceException();
    
    // if(!$this->database->select('preference_id', 'pagamento', "preference_id = '{$this->preference->id}'"))
    //   $this->database->insert('pagamento', [
    // ]);  

    return $this->respondWithData(["preference_id" => $this->preference->id]);
  }

}
