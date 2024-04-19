<?php

declare(strict_types= 1);

namespace App\Application\Actions\ActivityStudent;

use App\Application\Actions\Action;
use App\Domain\ActivityStudent\ActivityStudentRepository;
use App\Domain\Local\LocalRepository;
use App\Domain\User\UserRepository;
use Psr\Log\LoggerInterface;
use App\Database\DatabaseInterface;

abstract class ActivityStudentAction extends Action {

    protected ActivityStudentRepository $activityStudentRepository;
    protected UserRepository $userRepository;
    protected LocalRepository $localRepository;
    public function __construct(ActivityStudentRepository $activityStudentRepository, UserRepository $userRepository, LocalRepository $localRepository, LoggerInterface $logger, DatabaseInterface $database){
        parent::__construct($logger, $database);
        $this->activityStudentRepository = $activityStudentRepository;
        $this->userRepository = $userRepository;
        $this->localRepository = $localRepository;
    }
}