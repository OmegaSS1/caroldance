<?php 

declare(strict_types=1);

namespace App\Infrastructure\Persistence\MercadoPago;

use App\Database\DatabaseInterface;
use App\Domain\MercadoPago\MercadoPagoItem;
use App\Domain\MercadoPago\MercadoPagoItemNotFoundException;
use App\Domain\MercadoPago\MercadoPagoItemRepository;

class DataMercadoPagoRepository implements MercadoPagoItemRepository {

  /**
   * @var MercadoPagoItem[]
   */
  private array $items;

  /**
   * @param DatabaseInterface $database;
   * @param 
   */
  public function __construct(DatabaseInterface $database){
    $data = $database->select('*', 'mercadopago_item');
    foreach($data as $v){
      $this->items[$v['id']] = new MercadoPagoItem((int)$v['id'], (string)$v['category_id'], (string)$v['currency_id'], (string)$v['picture_url'], (string)$v['title'], (float)$v['unit_price'], (string)$v['date_created']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function findAll(): array{
    return array_values($this->items);
  }

  /**
   * {@inheritdoc}
   */
  public function findItemOfId(int $id): MercadoPagoItem {
    if(!isset($this->items[$id])){
      throw new MercadoPagoItemNotFoundException();
    }

    return $this->items[$id];
  }
}