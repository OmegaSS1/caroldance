<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Database\DatabaseInterface;
use App\Domain\ProfileUser\ProfileUserRepository;
use App\Domain\User\UserRepository;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
    protected UserRepository $userRepository;
    protected ProfileUserRepository $profileUserRepository;
    public function __construct(LoggerInterface $logger, DatabaseInterface $database, UserRepository $userRepository, ProfileUserRepository $profileUserRepository)
    {
        parent::__construct($logger, $database);
        $this->userRepository = $userRepository;
        $this->profileUserRepository = $profileUserRepository;
    }
}
