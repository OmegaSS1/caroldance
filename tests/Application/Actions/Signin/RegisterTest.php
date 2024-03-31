<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Signin;

use App\Application\Traits\Helper;
use App\Database\DatabaseInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\TestCase;

class RegisterTest extends TestCase {
  protected $db;

  protected function setUp() : void {
    parent::setUp();
    $this->database = $this->prophesize(DatabaseInterface::class);
    define('ENV', parse_ini_file(__DIR__. '/../../../../.env'));
    define('IP', $_SERVER['REMOTE_ADDR'] ?? $_SERVER['REMOTE_HOST'] ?? '127.0.0.1');
  }
  public function testAction(){
    $app = $this->getAppInstance();

    $token_csrf = "2d1eecc56e1111e98128349da7f27c361d35f7d1affb9bfe519d22bdb9a6b864";

    $requestFactory = new ServerRequestFactory();
    $request = $requestFactory->createServerRequest('POST', '/appteste/signin/register')
                ->withHeader("X-Csrf-Token", $token_csrf)
                ->withHeader("Content-Type", "application/json")
                ->withParsedBody(["email" => "teste_silva@hotmail.com", "senha" => "123456", "confirmarSenha" => "123456"]);
    $response = $app->handle($request);

    $this->assertEquals(200, $response->getStatusCode());
  }
}