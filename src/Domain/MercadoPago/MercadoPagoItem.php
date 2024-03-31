<?php

declare(strict_types=1);

namespace App\Domain\MercadoPago;
use JsonSerializable;

class MercadoPagoItem implements JsonSerializable {

  private ?int $id;
  private string $category_id;
  private string $currency_id;
  private string $picture_url;
  private string $title;
  private float $unit_price;
  private string $date_created;

  public function  __construct(?int $id, string $category_id, string $currency_id, string $picture_url, string $title, ?float $unit_price, string $date_created){
    $this->id           = $id;
    $this->category_id  = $category_id;
    $this->currency_id  = strtoupper($currency_id);
    $this->picture_url  = $picture_url;
    $this->title        = ucfirst($title);
    $this->unit_price   = $unit_price;
    $this->date_created = $date_created;
  }

  public function getId(): ?int{
    return $this->id;
  }

  public function getCategoryId(): string{
    return $this->category_id;
  }

  public function getCurrencyId(): string{
    return $this->currency_id;
  }

  public function getPicturyUrl(): string{
    return $this->picture_url;
  }

  public function getTitle(): string{
    return $this->title;
  }

  public function getUnitPrice(): ?float{
    return $this->unit_price;
  }

  public function getDateCreated(): string{
    return $this->date_created;
  }

  #[\ReturnTypeWillChange]
  public function jsonSerialize(): array
  {
      return [
          'id'           => $this->id,
          'category_id'  => $this->category_id,
          'currency_id'  => $this->currency_id,
          'picture_url'  => $this->picture_url,
          'title'        => $this->title,
          'unit_price'   => $this->unit_price,
          'date_created' => $this->date_created
      ];
  }

}