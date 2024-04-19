<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Local;

use App\Domain\Local\Local;
use App\Domain\Local\LocalNotFoundException;
use App\Domain\Local\LocalRepository;

use App\Database\DatabaseInterface;

class DataLocalRepository implements LocalRepository
{
    /**
     * @var Local[]
     */
    private array $local = [];

    /**
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database)
    {
        $data = $database->select('*', 'local');
        foreach ($data as $v){
            $this->local[$v['id']] = new Local(
                (int)    $v['id'],
                (string) $v['nome'],
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
        return array_values($this->local);
    }

    /**
     * {@inheritdoc}
     */
    public function findLocalById(int $id): Local
    {
        if (!isset($this->local[$id])) {
            throw new LocalNotFoundException();
        }

        return $this->local[$id];
    }
}