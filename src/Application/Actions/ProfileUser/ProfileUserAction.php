<?php

declare(strict_types=1);

namespace App\Application\Actions\ProfileUser;
use App\Application\Actions\Action;
use App\Database\DatabaseInterface;
use App\Domain\ProfileUser\ProfileUserRepository;
use Psr\Log\LoggerInterface;

abstract class ProfileUserAction extends Action {

    protected ProfileUserRepository $profileUserRepository;
    public function __construct(LoggerInterface $loggerInterface, DatabaseInterface $database, ProfileUserRepository $profileUserRepository){
        parent::__construct($loggerInterface, $database);
        $this->profileUserRepository = $profileUserRepository;
    }
}