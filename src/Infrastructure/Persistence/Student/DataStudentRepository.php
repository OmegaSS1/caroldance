<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Student;
use App\Domain\Student\Student;
use App\Domain\Student\StudentRepository;
use App\Database\DatabaseInterface;

class DataStudentRepository implements StudentRepository {

    /**
     * @var Student[]
     */
    private array $students = [];

    /**
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database){
        $data = $database->select('*', 'aluno');
        foreach ($data as $v) {
            $this->students[$v['id']] = new Student(
                (int)    $v['id'], 
                (string) $v['nome'], 
                (string) $v['sobrenome'], 
                (string) $v['data_nascimento'], 
                (string) $v['cpf'], 
                (int)    $v['servico_aluno_id'],
                (string) $v['dh_criacao'],
                (string) $v['dh_atualizacao'],
                (int)    $v['status']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(): array {
        return array_values($this->students);
    }

        /**
     * {@inheritDoc}
     */
    public function findUserByCpf(string $cpf)
    {
        $cpfArray = array_map(function($v){ return $v->getCpf(); }, $this->students);
        $key = array_search($cpf, $cpfArray, true);
        
        if ($key === false) {
            return false;
        }

        return $this->students[$key];
    }
}