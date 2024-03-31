<?php

declare(strict_types=1);

namespace App\Domain\User;

use JsonSerializable;

class User implements JsonSerializable
{
    private ?int $id;
    private string $nome;
    private string $sobrenome;
    private string $dataNascimento;
    private string $email;
    private string $cpf;
    private ?int $perfilUsuarioId;
    private string $telefoneWhatsapp;
    private string $telefoneRecado;
    private string $senha;
    private string $tokenRedefinicaoSenha;
    private string $dhCriacao;
    private string $dhAtualizacao;
    private ?int $status;

    public function __construct(?int $id, string $nome, string $sobrenome, string $dataNascimento, string $email, string $cpf, ?int $perfilUsuarioId, string $telefoneWhatsapp, string $telefoneRecado, string $senha, string $tokenRedefinicaoSenha, string $dhCriacao, string $dhAtualizacao, ?int $status)
    {
        $this->id                    = $id;
        $this->nome                  = ucfirst($nome);
        $this->sobrenome             = ucfirst($sobrenome);
        $this->dataNascimento        = $dataNascimento;
        $this->email                 = strtolower($email);
        $this->cpf                   = preg_replace('/\D/', '', $cpf);
        $this->perfilUsuarioId       = $perfilUsuarioId;
        $this->telefoneWhatsapp      = preg_replace('/\D/', '', $telefoneWhatsapp);
        $this->telefoneRecado        = preg_replace('/\D/', '', $telefoneRecado);
        $this->senha                 = $senha;
        $this->tokenRedefinicaoSenha = $tokenRedefinicaoSenha;
        $this->dhCriacao             = $dhCriacao;
        $this->dhAtualizacao         = $dhAtualizacao;
        $this->status                = $status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getSobrenome(): string
    {
        return $this->sobrenome;
    }

    public function getDataNascimento(): string
    {
        return $this->dataNascimento;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCpf(): string
    {
        return $this->cpf;
    }

    public function getPerfilUsuarioId(): ?int
    {
        return $this->perfilUsuarioId;
    }

    public function getTelefoneWhatsapp(): string
    {
        return $this->telefoneWhatsapp;
    }

    public function getTelefoneRecado(): string
    {
        return $this->telefoneRecado;
    }

    public function getSenha(): string
    {
        return $this->senha;
    }

    public function getTokenRedefinicaoSenha(): string
    {
        return $this->tokenRedefinicaoSenha;
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
            'id'                    => $this->id,
            'nome'                  => $this->nome,
            'sobrenome'             => $this->sobrenome,
            'dataNascimento'        => $this->dataNascimento,
            'email'                 => $this->email,
            'cpf'                   => $this->cpf,
            'perfilUsuarioId'       => $this->perfilUsuarioId,
            'telefoneWhatsapp'      => $this->telefoneWhatsapp,
            'telefoneRecado'        => $this->telefoneRecado,
            'senha'                 => $this->senha,
            'tokenRedefinicaoSenha' => $this->tokenRedefinicaoSenha,
            'dhCriacao'             => $this->dhCriacao,
            'dhAtualizacao'         => $this->dhAtualizacao,
            'status'                => $this->status
        ];
    }
}
