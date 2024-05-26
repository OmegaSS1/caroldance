<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;

use App\Domain\DomainException\CustomDomainException;
use Psr\Http\Message\ResponseInterface as Response;

class ClientTicketValidateTicketAction extends ClientTicketAction {

    public function action(): Response {
        $form = $this->validateForm($this->post($this->request));

        foreach($form['assentos'] as $v){
            if(!$seat = $this->ticketRepository->findTicketBySeat($v)){
                throw new CustomDomainException('Assento não localizado!');
            }

            else if(!$data = $this->database->select('ingresso_validado', 'cliente_ingresso', "ingresso_id = " . $seat->getId(), "periodo = '{$form['periodo']}' AND status = 1")){
                throw new CustomDomainException('Ingressos não localizados!');
            }
            else {
                $tickets[] = $data[0]['ingresso_validado'];
            }
        }
        
        if(in_array(1, $tickets)) $response = false;
        else $response = true;

        return $this->respondWithData([$response]);
    }

    private function validateForm(array $form): array{
        $this->validKeysForm($form, ['assentos', 'periodo']);
        return $form;
    }
}