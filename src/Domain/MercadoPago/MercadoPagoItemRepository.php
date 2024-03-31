<?php

declare(strict_types=1);

namespace App\Domain\MercadoPago;

interface MercadoPagoItemRepository {

  /**
   * @return  MercadoPagoItem[]
   */
  public function findAll(): array;

  /**
   * @param int $id
   * @return MercadoPagoItem
   * @throws MercadoPagoItemNotFoundException
   */
  public function findItemOfId(int $id): MercadoPagoItem;

}