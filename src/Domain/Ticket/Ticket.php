<?php

declare(strict_types=1);

namespace App\Domain\Ticket;

use JsonSerializable;

class Ticket implements JsonSerializable
{
    private int $id;
    private string $letra;
    private string $assento;
    private string $dataInclusao;
    private string $dataAtualizacao;

    public function __construct(
        int    $id,
        string $letra,
        string $assento,
        string $dataInclusao,
        string $dataAtualizacao
    ) {
        $this->id = $id;
        $this->letra = $letra;
        $this->assento = $assento;
        $this->dataInclusao = $dataInclusao;
        $this->dataAtualizacao = $dataAtualizacao;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getLetra(): string
    {
        return $this->letra;
    }

    public function getAssento(): string
    {
        return $this->assento;
    }

    public function getDataInclusao(): string
    {
        return $this->dataInclusao;
    }

    public function getDataAtualizacao(): string
    {
        return $this->dataAtualizacao;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'letra' => $this->letra,
            'assento' => $this->assento,
            'dataInclusao' => $this->dataInclusao,
            'dataAtualizacao' => $this->dataAtualizacao,
        ];
    }
}
