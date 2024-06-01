<?php

declare(strict_types=1);

namespace App\Domain\ParkingTicket;

use JsonSerializable;

class ParkingTicket implements JsonSerializable
{
    private int $id;
    private int $alunoId;
    private string $periodo;
    private string $nome;
    private string $cpf;
    private string $email;
    private int $valor;
    private string $statusPagamento;
    private string $dataInclusao;
    private string $dataAtualizacao;

    public function __construct(
        int    $id,
        int    $alunoId,
        string $periodo,
        string $nome,
        string $cpf,
        string $email,
        int    $valor,
        string $statusPagamento,
        string $dataInclusao,
        string $dataAtualizacao
    ) {
        $this->id = $id;
        $this->alunoId = $alunoId;
        $this->periodo = $periodo;
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->email = $email;
        $this->valor = $valor;
        $this->statusPagamento = $statusPagamento;
        $this->dataInclusao = $dataInclusao;
        $this->dataAtualizacao = $dataAtualizacao;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAlunoId(): int
    {
        return $this->alunoId;
    }
    public function getPeriodo(): string
    {
        return $this->periodo;
    }
    public function getNome(): string
    {
        return $this->nome;
    }

    public function getCpf(): string
    {
        return $this->cpf;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
    public function getValor(): int
    {
        return $this->valor;
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
            'id' => $this->id,
            'alunoId' => $this->alunoId,
            'periodo' => $this->periodo,
            'nome' => $this->nome,
            'cpf' => $this->cpf,
            'email' => $this->email,
            'valor' => $this->valor,
            'statusPagamento' => $this->statusPagamento,
            'dataInclusao' => $this->dataInclusao,
            'dataAtualizacao' => $this->dataAtualizacao,
        ];
    }
}
