<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Application\Traits\Helper;
use App\Database\DatabaseInterface;
use App\Domain\User\UserRepository;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
    use Helper;
    protected UserRepository $userRepository;
    protected  DatabaseInterface $database;
    
    public function __construct(LoggerInterface $logger, UserRepository $userRepository, DatabaseInterface $database)
    {
        parent::__construct($logger, $database);
        $this->userRepository = $userRepository;
    }
}
