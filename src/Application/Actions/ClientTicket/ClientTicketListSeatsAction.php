<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;

use Psr\Http\Message\ResponseInterface as Response;

class ClientTicketListSeatsAction extends ClientTicketAction {

    public function action(): Response {

        $tickets = $this->ticketRepository->findAll();

        $freeSeats      = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $sessoes        = ["08/06/2024 - SESSAO 1", "08/06/2024 - SESSAO 2"];
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

        // foreach($sessoes as $k => $sessao){
        //     foreach($tickets as $k => $v){
        //         $seatInfo = [
        //             "id" => 2,
        //             "name" => $v->getAssento(),
        //             "disponivel" => false,
        //             "valor" => 0
        //         ];
        //         $data[trim(explode('SESSAO', $sessao)[1])][] = $seatInfo;

                // $initialLetter = $v->getLetra();

        //         if($initialLetter != $finalLetter){
        //             if(in_array($finalLetter, $lettersWithX)){
        //                 $l = $letter[0];
        //                 unset($letter[0]);
        //                 $impar = ['X', $finalLetter];
        //                 $par   = ['X', $finalLetter];
        //                 foreach($impar as $i){
        //                     $add = [
        //                         "id" => 2,
        //                         "name" => $i,
        //                         "disponivel" => false,
        //                         "valor" => 0
        //                     ];
        //                     array_unshift($letter, $add);
        //                 }

        //                 array_unshift($letter,["id" => 1,"name" => $l['name'],"disponivel" => $l['disponivel'],"valor" => $l['valor']]);
        //                 foreach($par as $i){
        //                     $add = [
        //                         "id" => 1,
        //                         "name" => $i,
        //                         "disponivel" => false,
        //                         "valor" => 0
        //                     ];
        //                     array_unshift($letter, $add);
        //                 }
        //             }
        //             else{
        //                 for($i=1; $i > -1; $i--){
        //                     $add = [
        //                         "id" => $i,
        //                         "name" => $finalLetter,
        //                         "disponivel" => false,
        //                         "valor" => 0
        //                     ];
        //                     array_unshift($letter, $add);

        //                 }
        //             }

        //             $data[trim(explode('SESSAO', $sessao)[1])][] = $letter;
        //             $letter = [];
        //         }

        //         $valor     = 0;
        //         $isAvaible = true;
        //         if($seat = $this->clientTicketRepository->findClientTicketBySeatId($v->getId())){
        //             foreach($seat as $s){
        //                 if($s->getStatus() == 1 and $s->getPeriodo() == $sessao){
        //                     $isAvaible = false;
        //                     $valor     = $s->getValor();
        //                 }
        //             }
        //         }
        //         else {
        //             if(!in_array($v->getLetra(), $freeSeats)){
        //                 $valor = 30;
        //             }
        //         }

        //         $letter[] = [
        //             "id" => $v->getId(),
        //             "name" => $v->getAssento(),
        //             "disponivel" => $isAvaible,
        //             "valor" => $valor
        //         ];

        //         $finalLetter = $v->getLetra();
            // }
        // }

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
                }
            }
            else{
                if(!in_array($letter, $freeSeats))
                    $valor = 30;
            }
            $data[$k] = ["id" => $v->getId(), "name" => $v->getAssento(), "disponivel" => $isAvaible, "valor" => $valor];

        }
    }
}