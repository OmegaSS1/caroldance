<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;

use App\Database\DatabaseInterface;
use Psr\Log\LoggerInterface;

class DataUserRepository implements UserRepository
{
    /**
     * @var User[]
     */
    private array $users = [];
    private LoggerInterface $logger;

    /**
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $data = $database->select('*', 'usuario');
        foreach ($data as $v){
            $this->users[$v['id']] = new User(
                (int)    $v['id'], 
                (string) $v['nome'], 
                (string) $v['sobrenome'], 
                (string) $v['data_nascimento'], 
                (string) $v['email'], 
                (string) $v['cpf'], 
                (int)    $v['perfil_usuario_id'], 
                (string) $v['telefone_whatsapp'], 
                (string) $v['telefone_recado'], 
                (string) $v['senha'], 
                (string) $v['token_redefinicao_senha'], 
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
        return array_values($this->users);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserById(int $id): User
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
        $cpfArray = array_map(function($v){ return $v->getCpf(); }, $this->users);
        $key = array_search($cpf, $cpfArray, true);
        
        if ($key === false) {
            return false;
        }

        return $this->users[$key];
    }

        /**
     * {@inheritDoc}
     */
    public function findUserByEmail(string $email)
    {
        $emailArray = array_map(function($v){ return $v->getEmail(); }, $this->users);
        $key = array_search($email, $emailArray, true);
        
        if ($key === false) {
            return false;
        }

        return $this->users[$key];
    }
}