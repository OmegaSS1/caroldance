<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;
use App\Domain\DomainException\CustomDomainException;
use App\Domain\ParkingTicket\ParkingTicket;
use Psr\Http\Message\ResponseInterface as Response;

class ClientTicketConfirmPurchaseParkingAction extends ClientTicketAction {

    protected function action(): Response {
        $client = $this->validateForm($this->post($this->request));

        $this->database->update('estacionamento_ingresso', [
            "status_pagamento" => 'Concluido'
        ], 
        "id = " . $client->getId());

        $this->database->update('cliente_ingresso', [
            "estacionamento" => 1
        ], 
        "aluno_id = " . $client->getAlunoId(), 
        "periodo = '" . $client->getPeriodo() .  "' AND nome = '" . $client->getNome() . "' AND cpf  = '" . $client->getCpf() . "' AND email = '" . $client->getEmail() . "' AND status = 1");

        $bodyMail = "
        <b>Pedido Realizado com Sucesso!</b><br><br>
        Recebemos seu pagamento com sucesso! Abaixo estão os dados da sua compra.<br><br>
        <b>Item:</b> <br><br>
        Estacionamento - R$15 <br><br>
        <b>Dados: </b><br><br>
        Status: <b>Concluido </b><br>
        Data do Pedido: " . $client->getDataInclusao() . "<br>
        Forma de Pagamento: PIX <br>
        Valor Total: R$15 <br>
        Valido para: " . $client->getPeriodo();


        $this->sendMail("Carol Dance - Memórias", $bodyMail, [$client->getEmail()], [], ['vini15_silva@hotmail.com']);
        $this->database->commit();

        return $this->respondWithData();
    }

    private function validateForm(array $form): ParkingTicket {
        $this->validKeysForm($form, ['id']);

        $parking = $this->parkingTicketRepository->findParkingTicketById($form['id']);

        if($parking->getStatusPagamento() == 'Concluido')
            throw new CustomDomainException('O pagamento já foi concluido!');
        
        return $parking;
    }
}