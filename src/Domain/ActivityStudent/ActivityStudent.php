<?php

declare(strict_types=1);

namespace App\Domain\ActivityStudent;

use JsonSerializable;

class ActivityStudent implements JsonSerializable
{
    private int $id;
    private string $nome;
    private float $valor;
    private int $segunda;
    private int $terca;
    private int $quarta;
    private int $quinta;
    private int $sexta;
    private int $sabado;
    private int $domingo;
    private string $hInicial;
    private string $hFinal;
    private int $usuarioId;
    private int $localId;
    private string $dhCriacao;
    private string $dhAtualizacao;
    private int $status;

    public function __construct(
        int $id,
        string $nome,
        float $valor,
        int $segunda,
        int $terca,
        int $quarta,
        int $quinta,
        int $sexta,
        int $sabado,
        int $domingo,
        string $hInicial,
        string $hFinal,
        int $usuarioId,
        int $localId,
        string $dhCriacao,
        string $dhAtualizacao,
        int $status
    ) {
        $this->id            = $id;
        $this->nome          = $nome;
        $this->valor         = $valor;
        $this->segunda       = $segunda;
        $this->terca         = $terca;
        $this->quarta        = $quarta;
        $this->quinta        = $quinta;
        $this->sexta         = $sexta;
        $this->sabado        = $sabado;
        $this->domingo       = $domingo;
        $this->hInicial      = $hInicial;
        $this->hFinal        = $hFinal;
        $this->usuarioId     = $usuarioId;
        $this->localId       = $localId;
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

    public function getValor(): float
    {
        return $this->valor;
    }

    public function getSegunda(): int
    {
        return $this->segunda;
    }

    public function getTerca(): int
    {
        return $this->terca;
    }

    public function getQuarta(): int
    {
        return $this->quarta;
    }

    public function getQuinta(): int
    {
        return $this->quinta;
    }

    public function getSexta(): int
    {
        return $this->sexta;
    }

    public function getSabado(): int
    {
        return $this->sabado;
    }

    public function getDomingo(): int
    {
        return $this->domingo;
    }

    public function getHInicial(): string
    {
        return $this->hInicial;
    }

    public function getHFinal(): string
    {
        return $this->hFinal;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    public function getLocalId(): int
    {
        return $this->localId;
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
            'id'            => $this->id,
            'nome'          => $this->nome,
            'valor'         => $this->valor,
            'segunda'       => $this->segunda,
            'terca'         => $this->terca,
            'quarta'        => $this->quarta,
            'quinta'        => $this->quinta,
            'sexta'         => $this->sexta,
            'sabado'        => $this->sabado,
            'domingo'       => $this->domingo,
            'hInicial'      => $this->hInicial,
            'hFinal'        => $this->hFinal,
            'usuarioId'     => $this->usuarioId,
            'localId'       => $this->localId,
            'dhCriacao'     => $this->dhCriacao,
            'dhAtualizacao' => $this->dhAtualizacao,
            'status'        => $this->status
        ];
    }
}
