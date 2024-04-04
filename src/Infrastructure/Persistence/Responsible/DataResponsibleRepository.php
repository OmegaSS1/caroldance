<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Responsible;

use App\Domain\Responsible\Responsible;
use App\Domain\Responsible\ResponsibleRepository;
use App\Domain\Responsible\ResponsibleNotFoundException;
use App\Database\DatabaseInterface;

class DataResponsibleRepository implements ResponsibleRepository{

  /**
   * @var Responsible[]
   */
  private array $responsible = [];

  public function __construct(DatabaseInterface $database){
    $data = $database->select('*', 'responsavel');
    foreach($data as $v) {
      $this->responsible[$v['id']] = new Responsible(
        (int)    $v['id'],
        (int)    $v['usuario_id'],
        (int)    $v['aluno_id'],
        (int)    $v['parentesco_id'],
        (string) $v['dh_criacao'],
        (string) $v['dh_atualizacao'],
        (int)    $v['status'],
      );
    }
  }

  /**
   * {@inheritDoc}
   */
  public function findAll(): array {
    return  array_values($this->responsible);
  }

  /**
   * {@inheritDoc}
   */
  public function findUserOfId(int $id): Responsible {
    if (!isset($this->responsible[$id])) {
      throw new ResponsibleNotFoundException();
    }

    return $this->responsible[$id];
  }

}