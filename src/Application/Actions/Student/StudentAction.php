<?php

declare(strict_types=1);

namespace App\Application\Actions\Student;
use App\Application\Actions\Action;
use App\Database\DatabaseInterface;
use App\Domain\ActivityStudent\ActivityStudentRepository;
use App\Domain\MonthlyPayment\MonthlyPaymentRepository;
use App\Domain\Student\StudentRepository;
use Psr\Log\LoggerInterface;

abstract class StudentAction extends Action {

    protected StudentRepository $studentRepository;
    protected ActivityStudentRepository $activityStudentRepository;
    protected MonthlyPaymentRepository $monthlyPaymentRepository;
    public function __construct(LoggerInterface $loggerInterface, DatabaseInterface $database, StudentRepository $studentRepository, ActivityStudentRepository $activityStudentRepository, MonthlyPaymentRepository $monthlyPaymentRepository){
        parent::__construct($loggerInterface, $database);
        $this->studentRepository = $studentRepository;
        $this->activityStudentRepository = $activityStudentRepository;
        $this->monthlyPaymentRepository = $monthlyPaymentRepository;
    }
}