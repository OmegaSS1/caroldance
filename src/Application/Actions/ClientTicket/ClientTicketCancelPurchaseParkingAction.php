<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;
use App\Domain\DomainException\CustomDomainException;
use App\Domain\ParkingTicket\ParkingTicket;
use Psr\Http\Message\ResponseInterface as Response;

class ClientTicketCancelPurchaseParkingAction extends ClientTicketAction {

    protected function action(): Response {
        $client = $this->validateForm($this->post($this->request));

        $this->database->update('estacionamento_ingresso', [
            "status_pagamento" => 'Cancelado'
        ], 
        "id = " . $client->getId());

        $this->database->commit();

        return $this->respondWithData();
    }

    private function validateForm(array $form): ParkingTicket {
        $this->validKeysForm($form, ['id']);

        $parking = $this->parkingTicketRepository->findParkingTicketById($form['id']);

        if($parking->getStatusPagamento() == 'Cancelado')
            throw new CustomDomainException('O pagamento já foi cancelado!');
        
        return $parking;
    }
}