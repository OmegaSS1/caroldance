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
    private ?int $atividadeAlunoId;
    private string $dhCriacao;
    private string $dhAtualizacao;
    private ?int $status;

    public function __construct(?int $id, string $nome, string $sobrenome, string $dataNascimento, string $cpf, ?int $atividadeAlunoId, string $dhCriacao, string $dhAtualizacao, int $status) {
        $this->id               = $id;
        $this->nome             = $nome;
        $this->sobrenome        = $sobrenome;
        $this->dataNascimento   = $dataNascimento;
        $this->cpf              = $cpf;
        $this->atividadeAlunoId = $atividadeAlunoId;
        $this->dhCriacao        = $dhCriacao;
        $this->dhAtualizacao    = $dhAtualizacao;
        $this->status           = $status;
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
    public function getAtividadeAlunoId(): ?int {
        return $this->atividadeAlunoId;
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
            'atividadeAlunoId'      => $this->atividadeAlunoId,
            'dhCriacao'             => $this->dhCriacao,
            'dhAtualizacao'         => $this->dhAtualizacao,
            'status'                => $this->status
        ];
    }
}
