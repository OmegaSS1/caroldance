<?php

declare(strict_types=1);

namespace App\Application\Actions\MonthlyPayment;

use App\Application\Actions\Action;
use App\Database\DatabaseInterface;
use App\Domain\ActivityStudent\ActivityStudentRepository;
use App\Domain\MonthlyPayment\MonthlyPaymentRepository;
use App\Domain\Student\StudentRepository;
use Psr\Log\LoggerInterface;

abstract class MonthlyPaymentAction extends Action
{
    protected MonthlyPaymentRepository $monthlyPaymentRepository;
    protected StudentRepository $studentRepository;
    protected ActivityStudentRepository $activityStudentRepository;
    public function __construct(LoggerInterface $logger, DatabaseInterface $database, MonthlyPaymentRepository $monthlyPaymentRepository, StudentRepository $studentRepository, ActivityStudentRepository $activityStudentRepository)
    {
        parent::__construct($logger, $database);
        $this->monthlyPaymentRepository = $monthlyPaymentRepository;
        $this->studentRepository = $studentRepository;
        $this->activityStudentRepository = $activityStudentRepository;
    }
}
