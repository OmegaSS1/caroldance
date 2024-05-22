<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;
use App\Domain\DomainException\CustomDomainException;
use Psr\Http\Message\ResponseInterface as Response;

class ClientTicketCancelPurchaseAction extends ClientTicketAction {

    protected function action(): Response {
        $form = $this->validateForm($this->post($this->request));

        foreach($form['assentos'] as $v){
            $seatName = $this->ticketRepository->findTicketById($v)->getAssento();
            if(!!$this->database->select('*', 'cliente_ingresso', "ingresso_id = $v", "periodo = '{$form['periodo']}' AND status = 1")){
                $this->database->update('cliente_ingresso', [
                    "status_pagamento" => "Cancelado",
                    "status" => 0
                ],
                "ingresso_id = $v", "periodo = '{$form['periodo']}'");
            }
            else{
                throw new CustomDomainException("O assento $seatName não foi localizado na base de dados!");
            }
        }

        $this->database->commit();

        return $this->respondWithData();
    }

    private function validateForm(array $form): array {
        $this->validKeysForm($form, ['periodo', 'assentos']);

        foreach($form['assentos'] as $k =>$v){
            if(!!$seat = $this->ticketRepository->findTicketBySeat(strtoupper($v))){
                $form['assentos'][$k] = $seat->getId();
            }
            else {
                throw new CustomDomainException("O assento $v não foi localizado!");
            }
        }

        return $form;
    }
}