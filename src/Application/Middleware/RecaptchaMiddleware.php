<?php

declare(strict_types=1);

namespace App\Application\Middleware;
use App\Application\Middleware\MiddlewareException\InvalidRecaptchaMiddleware;
use App\Application\Traits\Helper;
use Psr\Http\Message\ResponseInterface as Response;
class RecaptchaMiddleware extends ActionMiddleware{
  use Helper;

  protected function action(): Response {

    $data = $this->args['token'];

    $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode(ENV['SECRET_KEY_RECAPTCHAV2']) . '&response=' . urlencode($data['token']);
    $responseData = json_decode(file_get_contents($url));

    if($responseData->success){
      $this->logger->info("[Middleware - IP ".IP."]", get_object_vars($responseData));
      return $this->respondWithData(['message' => 'Token verificado com sucesso!']);
    }
    
    $this->logger->info("[Middleware - IP ".IP."]", get_object_vars($responseData));
    throw new InvalidRecaptchaMiddleware($this->request);
  }
}
