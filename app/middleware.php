<?php

declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use App\Application\Middleware\ValidateTokenCSRFMiddleware;
use Slim\App;

return function (App $app) {
    $app->add(ValidateTokenCSRFMiddleware::class);
};
