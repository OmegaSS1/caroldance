<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => true,
                'logErrorDetails'     => true,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                "memory_limit" => '256M',
                "max_execution_time" => "90",
                "mb_internal_encoding" => "UTF-8",
                "locale" => [
                    "category" => LC_ALL,
                    "locales" => "pt_BR", "pt_BR.utf-8", "portuguese"
                ],
                "date_default_timezone_set" => "America/Sao_Paulo",
                "html" => [
                    "path"  => __DIR__."/../public/src",
                    "cache" => __DIR__."/../public/cache"
                ]
            ]);
        }
    ]);
};
