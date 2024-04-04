<?php

declare(strict_types=1);

namespace App\Domain\Responsible;

use JsonSerializable;

class Responsible implements JsonSerializable
{

  private ?int $id;
  private ?int $usuarioId;
  private ?int $alunoId;
  private ?int $parentescoId;
  private string $dhCriacao;
  private string $dhAtualizacao;
  private ?int $status;

  public function __construct(
    ?int $id,
    ?int $usuarioId,
    ?int $alunoId,
    ?int $parentescoId,
    string $dhCriacao,
    string $dhAtualizacao,
    ?int $status
  ) {
    $this->id = $id;
    $this->usuarioId = $usuarioId;
    $this->alunoId = $alunoId;
    $this->parentescoId = $parentescoId;
    $this->dhCriacao = $dhCriacao;
    $this->dhAtualizacao = $dhAtualizacao;
    $this->status = $status;
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getUsuarioId(): ?int
  {
    return $this->usuarioId;
  }

  public function getAlunoId(): ?int
  {
    return $this->alunoId;
  }

  public function getParentescoId(): ?int
  {
    return $this->parentescoId;
  }

  public function getDhCriacao(): string
  {
    return $this->dhCriacao;
  }

  public function getDhAtualizacao(): string
  {
    return $this->dhAtualizacao;
  }

  public function getStatus(): ?int
  {
    return $this->status;
  }

  #[\ReturnTypeWillChange]
  public function jsonSerialize(): array
  {
    return [
      'id' => $this->id,
      'usuarioId' => $this->usuarioId,
      'alunoId' => $this->alunoId,
      'parentescoId' => $this->parentescoId,
      'dhCriacao' => $this->dhCriacao,
      'dhAtualizacao' => $this->dhAtualizacao,
      'status' => $this->status,
    ];
  }
}