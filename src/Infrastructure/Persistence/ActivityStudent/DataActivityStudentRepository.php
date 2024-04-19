<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\ActivityStudent;

use App\Domain\ActivityStudent\ActivityStudent;
use App\Domain\ActivityStudent\ActivityStudentNotFoundException;
use App\Domain\ActivityStudent\ActivityStudentRepository;

use App\Database\DatabaseInterface;

class DataActivityStudentRepository implements ActivityStudentRepository
{
    /**
     * @var ActivityStudent[]
     */
    private array $activityStudent = [];

    /**
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database)
    {
        $data = $database->select('*', 'atividade_aluno');
        foreach ($data as $v){
            $this->activityStudent[$v['id']] = new ActivityStudent(
                (int)    $v['id'],
                (string) $v['nome'],
                (float)  $v['valor'],
                (int)    $v['segunda'],
                (int)    $v['terca'],
                (int)    $v['quarta'],
                (int)    $v['quinta'],
                (int)    $v['sexta'],
                (int)    $v['sabado'],
                (int)    $v['domingo'],
                (string) $v['h_inicial'],
                (string) $v['h_final'],
                (int)    $v['usuario_id'],
                (int)    $v['local_id'],
                (string) $v['dh_criacao'],
                (string) $v['dh_atualizacao'],
                (int)    $v['status']
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return array_values($this->activityStudent);
    }

    /**
     * {@inheritDoc}
     */
    public function findActivityStudentById(int $id): ActivityStudent
    {
        if (!isset($this->activityStudent[$id])) {
            throw new ActivityStudentNotFoundException();
        }

        return $this->activityStudent[$id];
    }
}