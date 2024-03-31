<?php

declare(strict_types=1);

namespace App\Application\Services\Google;

use App\Application\Actions\Action;
use App\Domain\User\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Google\Client as GoogleClient;

class GoogleOAuth extends Action{

  protected function action(): Response {

    $token = $this->args['token'] ?? '';

    $client = new GoogleClient();
    $client->setClientId(ENV['GOOGLE_CLIENT_ID']);
    $client->setClientSecret(ENV['GOOGLE_SECRET_ID']);
    $user = $client->verifyIdToken($token);

    if(!$user){
      $this->logger->info("[Google - IP ".IP."] Conta Google não encontrada!", $user);
      throw new UserNotFoundException('Erro ao validar sua conta Google!');
    }

    else if(!$this->database->select('*', 'usuario', "email='{$user['email']}'")){
      $this->database->insert('usuario', [
        "email" => $user['email'],
        "metodoLogin" => 'Google'
      ]);
      $this->logger->info("[Google - IP ".IP."] Conta Google cadastrada com sucesso!", $user);
    }
    return $this->respondWithData();
  }
}
