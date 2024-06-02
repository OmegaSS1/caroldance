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

        // $bodyMail = "
        // <b>Pedido Realizado com Sucesso!</b><br><br>
        // Recebemos seu pagamento com sucesso! Abaixo estão os dados da sua compra.<br><br>
        // <b>Item:</b> <br><br>
        // Estacionamento - R$15 <br><br>
        // <b>Dados: </b><br><br>
        // Status: <b>Concluido </b><br>
        // Data do Pedido: " . $client->getDataInclusao() . "<br>
        // Forma de Pagamento: PIX <br>
        // Valor Total: R$15 <br>
        // Valido para: " . $client->getPeriodo();


        // $this->sendMail("Carol Dance - Memórias", $bodyMail, [$client->getEmail()], [], ['vini15_silva@hotmail.com']);
        $this->database->commit();

        return $this->respondWithData();
    }

    private function validateForm(array $form): ParkingTicket {
        $this->validKeysForm($form, ['id']);

        $parking = $this->parkingTicketRepository->findParkingTicketById($form['id']);

        if($parking->getStatusPagamento() == 'Cancelado')
            throw new CustomDomainException('O pagamento já foi cancelado!');

        // else if($parking->getStatusPagamento() == 'Concluido')
        //     throw new CustomDomainException('Para pagamento concluido, o cancelamento não pode ser efetuado por aqui!');
        
        return $parking;
    }
}