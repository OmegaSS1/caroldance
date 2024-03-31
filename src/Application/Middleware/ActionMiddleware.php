<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Actions\Action;
use App\Application\Traits\Helper;
use App\Database\DatabaseInterface;
use Psr\Log\LoggerInterface;

abstract class ActionMiddleware extends Action{

  use Helper;
	protected string $table = "usuario";
  protected string $column = "email";

  public function __construct(LoggerInterface $logger, DatabaseInterface $database){
		parent::__construct($logger, $database);
  }
}
