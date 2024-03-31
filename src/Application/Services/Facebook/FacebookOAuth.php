<?php

declare(strict_types=1);

namespace App\Application\Services\Facebook;

use App\Application\Actions\Action;
use App\Application\Traits\Helper;
use App\Domain\User\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class FacebookOAuth extends Action{
  use Helper;
  protected function action(): Response {

    $token = $this->args['token'] ?? '';

    $url = "https://graph.facebook.com/me?fields=id,name,email&access_token={$token}";
    $userData = json_decode(file_get_contents($url), true);
    
    if (isset($userData['error'])) {
      $this->logger->error("[Facebook - IP ".IP."] Erro ao validar token do Facebook", $userData);
      throw new UserNotFoundException('Houve um erro ao validar sua conta do Facebook');
    } 
    // else if(!$this->database->select('*', 'usuario', "email='{$user['email']}'")){
    //   $this->database->insert('usuario', [
    //     "email" => $user['email'],
    //     "metodoLogin" => 'Google'
    //   ]);
      // $this->logger->info("[Google - IP ".IP."] Conta Google cadastrada com sucesso!", $user);
    // }
    return $this->respondWithData($userData);
  }
}
