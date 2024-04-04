<?php

declare(strict_types=1);

use App\Domain\Responsible\ResponsibleRepository;
use App\Domain\Student\StudentRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Student\DataStudentRepository;
use App\Infrastructure\Persistence\User\DataUserRepository;

use App\Database\DatabaseInterface;
use App\Database\DatabaseManager;
use App\Infrastructure\Persistence\Responsible\DataResponsibleRepository;
use App\View\View;

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        DatabaseInterface::class => \DI\autowire(DatabaseManager::class),
        UserRepository::class => \DI\autowire(DataUserRepository::class),
        ResponsibleRepository::class => \DI\autowire(DataResponsibleRepository::class),
        StudentRepository::class => \DI\autowire(DataStudentRepository::class),
        "html" => \DI\autowire(View::class)
    ]);
};
