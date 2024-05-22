<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;
use App\Domain\DomainException\CustomDomainException;
use Psr\Http\Message\ResponseInterface as Response;

class ClientTicketConfirmPurchaseAction extends ClientTicketAction {

    protected function action(): Response {
        $form = $this->validateForm($this->post($this->request));

        $ticketMail = "<b>Ingressos: </b><br><br>";
        $vTotal     = 0;

        foreach($form['assentos'] as $v){
            $seatName = $this->ticketRepository->findTicketById($v)->getAssento();
            if(!!$data = $this->database->select('*', 'cliente_ingresso', "ingresso_id = $v", "periodo = '{$form['periodo']}' AND status = 1")){
                if($data[0]['status_pagamento'] == 'Concluido') continue;
                else {
                    $this->database->update('cliente_ingresso', [
                        "status_pagamento" => "Concluido"
                    ],
                    "ingresso_id = $v", "periodo = '{$form['periodo']}'");
                    switch((int)$data[0]['valor']){
                        case 0:
                            $ticketMail .= "Assento $seatName - Cortesia";
                            $ticketMail .= "<br>";
                            break;
                        default:
                            $vTotal += 30;
                            $ticketMail .= "Assento $seatName - R$" . $v;
                            $ticketMail .= "<br>";
                    }
                    $form['email'] = $data[0]['email'];
                }
            }
            else{
                throw new CustomDomainException("O assento $seatName não foi localizado na base de dados!");
            }
        }

        $bodyMail = "
        <b>Pedido Realizado com Sucesso!</b><br><br>
        Recebemos seu pedido com sucesso, estamos aguardando o pagamento via pix e o envio do comprovante via
        WhatsApp: (71) 98690-4826<br><br>
        $ticketMail <br><br>
        <b>Dados: </b><br><br>
        Status: <b>Concluído</b><br>
        Data do Pedido: " . date('H:i:s d-m-Y') . "<br>
        Forma de Pagamento: PIX <br>
        Valor Total: $vTotal <br>
        Sessão: " . explode('SESSAO', $form['periodo'])[1];


        $this->sendMail("Carol Dance - Memórias", $bodyMail, [$form["email"]]);
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