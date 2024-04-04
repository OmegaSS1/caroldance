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
    $data = $database->select('*', 'responsavel');
    foreach($data as $v) {
      $this->profileUser[$v['id']] = new ProfileUser(
        (int)    $v['id'],
        (string) $v['name'],
        (string) $v['usuario_dashboard'],
        (string) $v['usuario_aluno'],
        (string) $v['adm_calendario'],
        (string) $v['adm_cadastro_aluno'],
        (string) $v['adm_cadastro_usuario'],
        (string) $v['adm_cadastro_atividade'],
        (string) $v['adm_relatorio_aluno'],
        (string) $v['adm_relatorio_usuario'],
        (string) $v['adm_relatorio_balancete'],
        (string) $v['adm_grafico_atividade_mensal'],
        (string) $v['adm_grafico_mensalidade_mes'],
        (string) $v['adm_grafico_atividade'],
        (string) $v['dh_criacao'],
        (string) $v['dh_atualizacao'],
        (string) $v['status']
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
  public function findUserOfId(int $id): ProfileUser {
    if (!isset($this->profileUser[$id])) {
      throw new ProfileUserNotFoundException();
    }

    return $this->profileUser[$id];
  }

}