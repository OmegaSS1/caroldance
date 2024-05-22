<?php

declare(strict_types=1);

namespace App\Domain\ClientTicket;

use JsonSerializable;

class ClientTicket implements JsonSerializable
{
    private int $id;
    private int $alunoId;
    private string $nome;
    private string $cpf;
    private string $email;
    private int $ingressoId;
    private int $valor;
    private string $tipo;
    private string $periodo;
    private string $statusPagamento;
    private int $estacionamento;
    private string $dataInclusao;
    private string $dataAtualizacao;
    private int $status;

    public function __construct(
        int    $id,
        int    $alunoId,
        string $nome,
        string $cpf,
        string $email,
        int    $ingressoId,
        int    $valor,
        string $tipo,
        string $periodo,
        string $statusPagamento,
        int    $estacionamento,
        string $dataInclusao,
        string $dataAtualizacao,
        int    $status
    ) {
        $this->id = $id;
        $this->alunoId = $alunoId;
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->email = $email;
        $this->ingressoId = $ingressoId;
        $this->valor = $valor;
        $this->tipo = $tipo;
        $this->periodo = $periodo;
        $this->statusPagamento = $statusPagamento;
        $this->estacionamento = $estacionamento;
        $this->dataInclusao = $dataInclusao;
        $this->dataAtualizacao = $dataAtualizacao;
        $this->status = $status;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAlunoId(): int
    {
        return $this->alunoId;
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
    public function getIngressoId(): int
    {
        return $this->ingressoId;
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

    public function getEstacionamento(): int
    {
        return $this->estacionamento;
    }

    public function getDataInclusao(): string
    {
        return $this->dataInclusao;
    }

    public function getDataAtualizacao(): string
    {
        return $this->dataAtualizacao;
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
            'alunoId' => $this->alunoId,
            'nome' => $this->nome,
            'cpf' => $this->cpf,
            'email' => $this->email,
            'ingressoId' => $this->ingressoId,
            'valor' => $this->valor,
            'tipo' => $this->tipo,
            'periodo' => $this->periodo,
            'statusPagamento' => $this->statusPagamento,
            'estacionamento' => $this->estacionamento,
            'dataInclusao' => $this->dataInclusao,
            'dataAtualizacao' => $this->dataAtualizacao,
            'status' => $this->status
        ];
    }
}
