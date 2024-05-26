<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;

use App\Domain\DomainException\CustomDomainException;
use Psr\Http\Message\ResponseInterface as Response;

class ClientTicketValidateTicketAction extends ClientTicketAction {

    public function action(): Response {
        $form = $this->validateForm($this->post($this->request));

        foreach($form['assentos'] as $v){
            if(!$seat = $this->ticketRepository->findTicketBySeat(trim($v))){
                throw new CustomDomainException('Assento não localizado!');
            }

            else if(!$data = $this->database->select('ingresso_validado', 'cliente_ingresso', "ingresso_id = " . $seat->getId(), "periodo = '{$form['periodo']}' AND status = 1")){
                throw new CustomDomainException('Ingressos não localizados!');
            }
            else {
                if($data[0]['ingresso_validado'] === 0){
                    $this->database->update('cliente_ingresso', ["ingresso_validado" => 1], "ingresso_id = " . $seat->getId(), "periodo = '{$form['periodo']}' AND status = 1");
                }
                $tickets[] = $data[0]['ingresso_validado'];
            }
        }
        
        $this->database->commit();
        if(in_array(1, $tickets)) $response = false;
        else $response = true;

        return $this->respondWithData([$response]);
    }

    private function validateForm(array $form): array{
        $this->validKeysForm($form, ['assentos', 'periodo']);
        return $form;
    }
}