<?php

declare(strict_types=1);

namespace Tests\App\Application\Middleware;

use App\Application\Middleware\GenerateTokenJWTMiddleware;
use Tests\TestCase;

class GenerateTokenJWTMiddlewareTest extends TestCase {

  public function testValidateIfGenerateTokenJWT(){
    define('ENV', parse_ini_file(__DIR__. '/../../.env'));
    $IP = $_SERVER['REMOTE_ADDR'] ?? $_SERVER['REMOTE_HOST'] ?? '127.0.0.1';

    $token = new GenerateTokenJWTMiddleware($IP, 'teste');
    $token = $token->getToken();

    $this->assertNotEmpty($token);
    $this->assertIsString($token);
  }
}