<?php

namespace App\Application\Middleware;

use App\Database\DatabaseInterface;

class GenerateTokenCSRFMiddleware {
  private string $token;
  private string $table = 'token_csrf'; 

  public function __construct(DatabaseInterface $database){
    $this->token = bin2hex(random_bytes(32));

		if (!!$database->select('token', $this->table, "ip = '" . IP . "'"))
			$database->update($this->table, ['token' => $this->token], "ip = '" . IP . "'");
		else
			$database->insert($this->table, ['ip' => IP, 'token' => $this->token]);

		$database->commit();

  }

  public function getToken(): string {
    return $this->token;
  }

}
