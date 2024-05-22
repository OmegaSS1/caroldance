<?php

declare(strict_types=1);

namespace App\Domain\ParkingTicket;

use JsonSerializable;

class ParkingTicket implements JsonSerializable
{
    private string $assento;
    private int $clienteIngressoId;
    private int $valor;
    private string $tipo;
    private string $periodo;
    private string $statusPagamento;
    private string $dataInclusao;
    private string $dataAtualizacao;

    public function __construct(
        string $assento,
        int    $clienteIngressoId,
        int    $valor,
        string $tipo,
        string $periodo,
        string $statusPagamento,
        string $dataInclusao,
        string $dataAtualizacao
    ) {
        $this->assento = $assento;
        $this->clienteIngressoId = $clienteIngressoId;
        $this->valor = $valor;
        $this->tipo = $tipo;
        $this->periodo = $periodo;
        $this->statusPagamento = $statusPagamento;
        $this->dataInclusao = $dataInclusao;
        $this->dataAtualizacao = $dataAtualizacao;
    }

    public function getAssento(): string
    {
        return $this->assento;
    }

    public function getClienteIngressoId(): int
    {
        return $this->clienteIngressoId;
    }

    public function getValor(): int
    {
        return $this->valor;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getPeriodo(): string
    {
        return $this->periodo;
    }

    public function getStatusPagamento(): string
    {
        return $this->statusPagamento;
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
            'assento' => $this->assento,
            'clienteIngressoId' => $this->clienteIngressoId,
            'valor' => $this->valor,
            'tipo' => $this->tipo,
            'periodo' => $this->periodo,
            'statusPagamento' => $this->statusPagamento,
            'dataInclusao' => $this->dataInclusao,
            'dataAtualizacao' => $this->dataAtualizacao,
        ];
    }
}
