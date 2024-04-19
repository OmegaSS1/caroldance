<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\ProfileUser;

use App\Domain\ProfileUser\ProfileUser;
use App\Domain\ProfileUser\ProfileUserRepository;
use App\Domain\ProfileUser\ProfileUserNotFoundException;
use App\Database\DatabaseInterface;

class DataProfileUserRepository implements ProfileUserRepository{

  /**
   * @var ProfileUser[]
   */
  private array $profileUser = [];

  public function __construct(DatabaseInterface $database){
    $data = $database->select('*', 'perfil_usuario');
    foreach($data as $v) {
      $this->profileUser[$v['id']] = new ProfileUser(
        (int)    $v['id'],
        (string) $v['nome'],
        (int)    $v['usuario_dashboard'],
        (int)    $v['usuario_aluno'],
        (int)    $v['adm_dashboard'],
        (int)    $v['adm_calendario'],
        (int)    $v['adm_cadastro_aluno'],
        (int)    $v['adm_cadastro_usuario'],
        (int)    $v['adm_cadastro_atividade'],
        (int)    $v['adm_relatorio_aluno'],
        (int)    $v['adm_relatorio_usuario'],
        (int)    $v['adm_relatorio_balancete'],
        (int)    $v['adm_grafico_atividade_mensal'],
        (int)    $v['adm_grafico_mensalidade_mes'],
        (int)    $v['adm_grafico_atividade'],
        (string) $v['dh_criacao'],
        (string) $v['dh_atualizacao'],
        (int)    $v['status']
      );
    }
  }

  /**
   * {@inheritDoc}
   */
  public function findAll(): array {
    return  array_values($this->profileUser);
  }

  /**
   * {@inheritDoc}
   */
  public function findProfileUserById(int $id): ProfileUser {
    if (!isset($this->profileUser[$id])) {
      throw new ProfileUserNotFoundException();
    }

    return $this->profileUser[$id];
  }

}