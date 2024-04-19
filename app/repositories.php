<?php

declare(strict_types=1);

use App\Domain\ActivityStudent\ActivityStudentRepository;
use App\Domain\Local\LocalRepository;
use App\Domain\MonthlyPayment\MonthlyPaymentRepository;
use App\Domain\ProfileUser\ProfileUserRepository;
use App\Domain\Responsible\ResponsibleRepository;
use App\Domain\Student\StudentRepository;
use App\Domain\User\UserRepository;

use App\Infrastructure\Persistence\ActivityStudent\DataActivityStudentRepository;
use App\Infrastructure\Persistence\Local\DataLocalRepository;
use App\Infrastructure\Persistence\MonthlyPayment\DataMonthlyPaymentRepository;
use App\Infrastructure\Persistence\ProfileUser\DataProfileUserRepository;
use App\Infrastructure\Persistence\Responsible\DataResponsibleRepository;
use App\Infrastructure\Persistence\Student\DataStudentRepository;
use App\Infrastructure\Persistence\User\DataUserRepository;

use App\Database\DatabaseInterface;
use App\Database\DatabaseManager;
use App\View\View;

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        DatabaseInterface::class         => \DI\autowire(DatabaseManager::class),
        UserRepository::class            => \DI\autowire(DataUserRepository::class),
        ResponsibleRepository::class     => \DI\autowire(DataResponsibleRepository::class),
        StudentRepository::class         => \DI\autowire(DataStudentRepository::class),
        ProfileUserRepository::class     => \DI\autowire(DataProfileUserRepository::class),
        ActivityStudentRepository::class => \DI\autowire(DataActivityStudentRepository::class),
        LocalRepository::class           => \DI\autowire(DataLocalRepository::class),
        MonthlyPaymentRepository::class  => \DI\autowire(DataMonthlyPaymentRepository::class),
        "html"                       => \DI\autowire(View::class)
    ]);
};
