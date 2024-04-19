<?php

declare(strict_types=1);

namespace App\Domain\MonthlyPayment;

use JsonSerializable;

class MonthlyPayment implements JsonSerializable
{
    private int $id;
    private int $alunoId;
    private string $mes;
    private string $dhVencimento;
    private ?string $dhPagamento;
    private string $statusPagamento;
    private ?string $observacoes;
    private string $dhCriacao;
    private string $dhAtualizacao;
    private int $status;

    public function __construct(
        int $id,
        int $alunoId,
        string $mes,
        string $dhVencimento,
        ?string $dhPagamento,
        string $statusPagamento,
        ?string $observacoes,
        string $dhCriacao,
        string $dhAtualizacao,
        int $status
    ) {
        $this->id               = $id;
        $this->alunoId          = $alunoId;
        $this->mes              = $mes;
        $this->dhVencimento     = $dhVencimento;
        $this->dhPagamento      = $dhPagamento;
        $this->statusPagamento  = $statusPagamento;
        $this->observacoes      = $observacoes;
        $this->dhCriacao        = $dhCriacao;
        $this->dhAtualizacao    = $dhAtualizacao;
        $this->status           = $status;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAlunoId(): int
    {
        return $this->alunoId;
    }

    public function getMes(): ?string
    {
        return $this->mes;
    }

    public function getDhVencimento(): string
    {
        return $this->dhVencimento;
    }

    public function getDhPagamento(): ?string
    {
        return $this->dhPagamento;
    }

    public function getStatusPagamento(): string
    {
        return $this->statusPagamento;
    }

    public function getObservacoes(): ?string
    {
        return $this->observacoes;
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
            'id'                 => $this->id,
            'aluno_id'           => $this->alunoId,
            'atividade_aluno_id' => $this->atividadeAlunoId,
            'valor'              => $this->valor,
            'mes'                => $this->mes,
            'dh_vencimento'      => $this->dhVencimento,
            'dh_pagamento'       => $this->dhPagamento,
            'status_pagamento'   => $this->statusPagamento,
            'observacoes'        => $this->observacoes,
            'dh_criacao'         => $this->dhCriacao,
            'dh_atualizacao'     => $this->dhAtualizacao,
            'status'             => $this->status
        ];
    }
}
