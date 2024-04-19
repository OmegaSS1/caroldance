<?php

declare(strict_types=1);

namespace App\Application\Actions\Signin;

use App\Application\Actions\Action;
use App\Database\DatabaseInterface;
use App\Domain\User\UserRepository;
use Psr\Log\LoggerInterface;

abstract class SigninAction extends Action
{
    protected UserRepository $userRepository;
    public function __construct(LoggerInterface $logger, UserRepository $userRepository, DatabaseInterface $database)
    {
        parent::__construct($logger, $database);
        $this->userRepository = $userRepository;
    }
}
