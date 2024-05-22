<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;

use Psr\Http\Message\ResponseInterface as Response;

class ClientTicketListAction extends ClientTicketAction {

    public function action(): Response {

        $tickets = $this->clientTicketRepository->findAllByQuery();

        

        return $this->respondWithData($tickets);
    }

}