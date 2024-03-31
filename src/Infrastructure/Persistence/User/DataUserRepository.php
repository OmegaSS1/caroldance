<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;

use App\Database\DatabaseInterface;

class DataUserRepository implements UserRepository
{
    /**
     * @var User[]
     */
    private array $users = [];

    /**
     * @param DatabaseInterface $database
     * @param User[]|null $users
     */
    public function __construct(DatabaseInterface $database)
    {
        $data = $database->select('*', 'usuario');
        foreach ($data as $v){
            $this->users[$v['id']] = new User(
                (int)    $v['id'], 
                (string) $v['nome'], 
                (string) $v['sobrenome'], 
                (string) $v['dataNascimento'], 
                (string) $v['email'], 
                (string) $v['cpf'], 
                (int)    $v['perfilUsuarioId'], 
                (string) $v['telefoneWhatsapp'], 
                (string) $v['telefoneRecado'], 
                (string) $v['senha'], 
                (string) $v['tokenRedefinicaoSenha'], 
                (string) $v['dhCriacao'], 
                (string) $v['dhAtualizacao'], 
                (int)    $v['status']
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return array_values($this->users);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserOfId(int $id): User
    {
        if (!isset($this->users[$id])) {
            throw new UserNotFoundException();
        }

        return $this->users[$id];
    }

    /**
     * {@inheritDoc}
     */
    public function findUserByCpf(string $cpf)
    {
        $cpf_array = array_map(function($v){ return $v->getCpf(); }, $this->users);
        $key = array_search($cpf, $cpf_array, true);
        
        if ($key === false) {
            return false;
        }

        return $this->users[$key];
    }
}