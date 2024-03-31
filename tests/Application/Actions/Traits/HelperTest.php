<?php

namespace Tests\Application\Traits;
use App\Application\Traits\Helper;
use App\Database\DatabaseInterface;
use Tests\TestCase;

class HelperTest extends TestCase {

  use Helper;
  private $db;
  protected function setUp() : void {
    parent::setUp();
    $this->database = $this->prophesize(DatabaseInterface::class);
    define('IP', $_SERVER['REMOTE_ADDR'] ?? $_SERVER['REMOTE_HOST'] ?? '127.0.0.1');

  }
  public function testIfGenerateTokenCSRF(){
    $token = $this->generateTokenCSRF(IP);

    $this->assertNotEmpty($token);
    $this->assertIsString($token);
  }
}