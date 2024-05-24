<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;
use Psr\Http\Message\ResponseInterface as Response;

class Teste  extends ClientTicketAction{

    protected function action(): Response {

        $this->sendMail('Teste', 'teste', ['vini15_silva@hotmail.com']);
        return $this->respondWithData();
    }

}