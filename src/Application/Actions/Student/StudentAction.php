<?php

declare(strict_types=1);

namespace App\Application\Actions\Student;
use App\Application\Actions\Action;
use App\Application\Traits\Helper;
use App\Database\DatabaseInterface;
use App\Domain\Student\StudentRepository;
use Psr\Log\LoggerInterface;

abstract class StudentAction extends Action {

    use Helper;
    protected StudentRepository $studentRepository;
    public function __construct(LoggerInterface $loggerInterface, DatabaseInterface $database, StudentRepository $studentRepository){
        parent::__construct($loggerInterface, $database);
        $this->studentRepository = $studentRepository;
    }
}