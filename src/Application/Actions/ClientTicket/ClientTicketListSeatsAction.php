<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;

use Psr\Http\Message\ResponseInterface as Response;

class ClientTicketListSeatsAction extends ClientTicketAction {

    public function action(): Response {

        $tickets = $this->ticketRepository->findAll();

        $freeSeats      = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $sessoes        = ["11/12/2024 - SESSAO 1", "11/12/2024 - SESSAO 2"];
        $splicePosition = [0, 1, 3, 4];

        for($s=1; $s < 3; $s++){
            $length = false;
            for($i=0; $i < count($tickets); $i++){
                $seatUnavaible = ["id" => 1, "name" => "", "disponivel" => false, "valor" => 0];
                $letter = $tickets[$i]->getLetra();
                
                if(!$length){
                    $data[$s][] = array_slice($tickets, $i, 22);
                    $lastkey = array_key_last($data[$s]);
                    $this->seatsInfo($data[$s][$lastkey], $sessoes[$s-1], $letter, $freeSeats);
                    
                    for($c=0; $c < 2; $c++){
                        $seatUnavaible["name"] = 'X';
                        $seatUnavaible["id"]   = $c;
                        array_unshift($data[$s][$lastkey], $seatUnavaible);
                        // array_splice($data[$s][$lastkey], $splicePosition[$c], 0, [$seatUnavaible]);
                        $seatUnavaible["name"] = $letter;
                        $seatUnavaible["id"]   = $c;
                        array_unshift($data[$s][$lastkey], $seatUnavaible);
                        // array_splice($data[$s][$lastkey], $splicePosition[$c], 0, [$seatUnavaible]);
                    }

                    $length = true;
                    $i += 21;
                }
                else{
                    $data[$s][] = array_slice($tickets, $i, 24);
                    $lastkey = array_key_last($data[$s]);
                    $this->seatsInfo($data[$s][$lastkey], $sessoes[$s-1], $letter, $freeSeats);
                    
                    for($c=0; $c < 1; $c++){
                        $seatUnavaible["name"] = $letter;
                        $seatUnavaible["id"]   = $c;
                        array_splice($data[$s][$lastkey], $splicePosition[$c], 0, [$seatUnavaible]);
                        $c++;
                        $seatUnavaible["name"] = $letter;
                        $seatUnavaible["id"]   = $c;
                        array_splice($data[$s][$lastkey], $splicePosition[$c], 0, [$seatUnavaible]);
                    }

                    $length = false;
                    $i += 23;
                }
            }
        }

        return $this->respondWithData($data);
    }

    private function seatsInfo(&$data, $sessao, $letter, $freeSeats){
        foreach($data as $k => $v){
            $isAvaible = true;
            $valor = 0;

            if($seat = $this->clientTicketRepository->findClientTicketBySeatId($v->getId())){
                foreach($seat as $s){
                    if($s->getStatus() == 1 and $s->getPeriodo() == $sessao){
                        $isAvaible = false;
                        $valor     = $s->getValor();
                    }
                    else {
                        if(!in_array($letter, $freeSeats))
                            $valor = 40;
                    }
                }
            }
            else{
                if(!in_array($letter, $freeSeats))
                    $valor = 40;
            }
            $data[$k] = ["id" => $v->getId(), "name" => $v->getAssento(), "disponivel" => $isAvaible, "valor" => 40];

        }
    }
}