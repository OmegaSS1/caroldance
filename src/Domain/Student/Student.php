<?php

declare(strict_types=1);

namespace App\Domain\Student;
use App\Database\DatabaseInterface;
use JsonSerializable;

class Student implements JsonSerializable {
    private ?int $id;
    private string $nome;
    private string $sobrenome;
    private string $dataNascimento;
    private string $cpf;
    private ?int $servicoAlunoId;
    private string $dhCriacao;
    private string $dhAtualizacao;
    private ?int $status;

    public function __construct(?int $id, string $nome, string $sobrenome, string $dataNascimento, string $cpf, ?int $servicoAlunoId, string $dhCriacao, string $dhAtualizacao, int $status) {
        $this->id = $id;
        $this->nome = $nome;
        $this->sobrenome = $sobrenome;
        $this->dataNascimento = $dhCriacao;
        $this->cpf = $cpf;
        $this->servicoAlunoId = $servicoAlunoId;
        $this->dhCriacao = $dhCriacao;
        $this->dhAtualizacao = $dhAtualizacao;
        $this->status = $status;
    }

    public function getId(): ?int {
        return $this->id;
    }
    public function getNome(): string {
        return $this->nome;
    }
    public function getSobrenome(): string {
        return $this->sobrenome;
    }
    public function getDataNascimento(): string {
        return $this->dataNascimento;
    }
    public function getCpf(): string {
        return $this->cpf;
    }
    public function getServicoAlunoId(): ?int {
        return $this->servicoAlunoId;
    }
    public function getDhCriacao(): string {
        return $this->dhCriacao;
    }
    public function getDhAtualizacao(): string {
        return $this->dhAtualizacao;
    }
    public function getStatus(): ?int {
        return $this->status;
    }
        
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id'                    => $this->id,
            'nome'                  => $this->nome,
            'sobrenome'             => $this->sobrenome,
            'dataNascimento'        => $this->dataNascimento,
            'cpf'                   => $this->cpf,
            'servicoAlunoId'        => $this->servicoAlunoId,
            'dhCriacao'             => $this->dhCriacao,
            'dhAtualizacao'         => $this->dhAtualizacao,
            'status'                => $this->status
        ];
    }
}
