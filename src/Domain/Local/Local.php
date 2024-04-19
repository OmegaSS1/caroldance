<?php

declare(strict_types=1);

namespace App\Domain\Local;

use JsonSerializable;

class Local implements JsonSerializable
{
    private int $id;
    private string $nome;
    private string $dhCriacao;
    private string $dhAtualizacao;
    private int $status;

    public function __construct(
        int $id,
        string $nome,
        string $dhCriacao,
        string $dhAtualizacao,
        int $status
    ) {
        $this->id            = $id;
        $this->nome          = $nome;
        $this->dhCriacao     = $dhCriacao;
        $this->dhAtualizacao = $dhAtualizacao;
        $this->status        = $status;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDhCriacao(): string
    {
        return $this->dhCriacao;
    }

    public function getDhAtualizacao(): string
    {
        return $this->dhAtualizacao;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'dhCriacao' => $this->dhCriacao,
            'dhAtualizacao' => $this->dhAtualizacao,
            'status' => $this->status
        ];
    }
}
