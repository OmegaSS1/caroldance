<?php
declare(strict_types=1);

namespace App\Domain\ProfileUser;

use JsonSerializable;

class ProfileUser implements JsonSerializable
{
  private ?int $id;
  private string $nome;
  private ?int $usuarioDashboard;
  private ?int $usuarioAluno;
  private ?int $admDashboard;
  private ?int $admCalendario;
  private ?int $admCadastroAluno;
  private ?int $admCadastroUsuario;
  private ?int $admCadastroAtividade;
  private ?int $admRelatorioAluno;
  private ?int $admRelatorioUsuario;
  private ?int $admRelatorioBalancete;
  private ?int $admGraficoAtividadeMensal;
  private ?int $admGraficoMensalidadeMes;
  private ?int $admGraficoAtividade;
  private string $dhCriacao;
  private string $dhAtualizacao;
  private ?int $status;

  public function __construct(
    ?int $id,
    string $nome,
    ?int $usuarioDashboard,
    ?int $usuarioAluno,
    ?int $admDashboard,
    ?int $admCalendario,
    ?int $admCadastroAluno,
    ?int $admCadastroUsuario,
    ?int $admCadastroAtividade,
    ?int $admRelatorioAluno,
    ?int $admRelatorioUsuario,
    ?int $admRelatorioBalancete,
    ?int $admGraficoAtividadeMensal,
    ?int $admGraficoMensalidadeMes,
    ?int $admGraficoAtividade,
    string $dhCriacao,
    string $dhAtualizacao,
    ?int $status
  ) {
    $this->id = $id;
    $this->nome = $nome;
    $this->usuarioDashboard = $usuarioDashboard;
    $this->usuarioAluno = $usuarioAluno;
    $this->admDashboard = $admDashboard;
    $this->admCalendario = $admCalendario;
    $this->admCadastroAluno = $admCadastroAluno;
    $this->admCadastroUsuario = $admCadastroUsuario;
    $this->admCadastroAtividade = $admCadastroAtividade;
    $this->admRelatorioAluno = $admRelatorioAluno;
    $this->admRelatorioUsuario = $admRelatorioUsuario;
    $this->admRelatorioBalancete = $admRelatorioBalancete;
    $this->admGraficoAtividadeMensal = $admGraficoAtividadeMensal;
    $this->admGraficoMensalidadeMes = $admGraficoMensalidadeMes;
    $this->admGraficoAtividade = $admGraficoAtividade;
    $this->dhCriacao = $dhCriacao;
    $this->dhAtualizacao = $dhAtualizacao;
    $this->status = $status;
  }

  public function getId(): ?int
  {
    return $this->id;
  }
  public function getNome(): string
  {
    return $this->nome;
  }
  public function getUsuarioDashboard(): ?int
  {
    return $this->usuarioDashboard;
  }
  public function getUsuarioAluno(): ?int
  {
    return $this->usuarioAluno;
  }
  public function getAdmDashboard(): ?int
  {
    return $this->admDashboard;
  }
  public function getAdmCalendario(): ?int
  {
    return $this->admCalendario;
  }
  public function getAdmCadastroAluno(): ?int
  {
    return $this->admCadastroAluno;
  }
  public function getAdmCadastroUsuario(): ?int
  {
    return $this->admCadastroUsuario;
  }
  public function getAdmCadastroAtividade(): ?int
  {
    return $this->admCadastroAtividade;
  }
  public function getAdmRelatorioAluno(): ?int
  {
    return $this->admRelatorioAluno;
  }
  public function getAdmRelatorioUsuario(): ?int
  {
    return $this->admRelatorioUsuario;
  }
  public function getAdmRelatorioBalancete(): ?int
  {
    return $this->admRelatorioBalancete;
  }
  public function getAdmGraficoAtividadeMensal(): ?int
  {
    return $this->admGraficoAtividadeMensal;
  }
  public function getAdmGraficoMensalidadeMes(): ?int
  {
    return $this->admGraficoMensalidadeMes;
  }
  public function getAdmGraficoAtividade(): ?int
  {
    return $this->admGraficoAtividade;
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
  public function jsonSerialize(): array
  {
    return [
      'id' => $this->id,
      'nome' => $this->nome,
      'usuarioDashboard' => $this->usuarioDashboard,
      'usuarioAluno' => $this->usuarioAluno,
      'admDashboard' => $this->admDashboard,
      'admCalendario' => $this->admCalendario,
      'admCadastroAluno' => $this->admCadastroAluno,
      'admCadastroUsuario' => $this->admCadastroUsuario,
      'admCadastroAtividade' => $this->admCadastroAtividade,
      'admRelatorioAluno' => $this->admRelatorioAluno,
      'admRelatorioUsuario' => $this->admRelatorioUsuario,
      'admRelatorioBalancete' => $this->admRelatorioBalancete,
      'admGraficoAtividadeMensal' => $this->admGraficoAtividadeMensal,
      'admGraficoMensalidadeMes' => $this->admGraficoMensalidadeMes,
      'admGraficoAtividade' => $this->admGraficoAtividade,
      'dhCriacao' => $this->dhCriacao,
      'dhAtualizacao' => $this->dhAtualizacao,
      'status' => $this->status,
    ];
  }
}