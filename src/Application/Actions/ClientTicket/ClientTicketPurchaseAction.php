<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;
use App\Domain\DomainException\CustomDomainException;
use Psr\Http\Message\ResponseInterface as Response;

class ClientTicketPurchaseAction extends ClientTicketAction {

    protected function action(): Response {
        $form = $this->validateForm($this->post($this->request));

        $ticketMail = "<b>Ingressos: </b><br><br>";
        $vTotal     = (array_count_values($form['assentos'])['40'] ?? 0) * 40;
        $fTotal     =  array_count_values($form['assentos'])['0']  ?? 0;


        foreach($form['assentos'] as $k => $v){
            $this->database->insert('cliente_ingresso', [
                "aluno_id" => $form['aluno'],
                "nome" => $form['nome'],
                "cpf" => $form['cpf'],
                "email" => $form['email'],
                "ingresso_id" => $k,
                "valor" => (int) $v,
                "tipo"  => (int) $v === 0 ? 'Cortesia' : 'Pago',
                "periodo" => $form['periodo'],
                "estacionamento" => $form['estacionamento'],
                "status_pagamento" => ($vTotal == 0 ? "Concluido": "Pendente")
            ]);

            
            $seatName = $this->ticketRepository->findTicketById((int)$k)->getAssento();
            switch($v){
                case 0:
                    $ticketMail .= "Assento $seatName - Cortesia";
                    $ticketMail .= "<br>";
                    break;
                default:
                    $ticketMail .= "Assento $seatName - R$" . $v;
                    $ticketMail .= "<br>";
            }
        }

        $estacionamento = "";
        if($form['estacionamento'] == '1'){
            $estacionamento .= "Estacionamento: R$15<br><br>";
            $vTotal += 15;
        }

        $pay  = "Recebemos seu pedido com sucesso, estamos aguardando o pagamento via pix e o envio do comprovante via WhatsApp: (71) 98690-4826<br><br>";
        $free = "Recebemos seu pedido com sucesso! 🎉 As suas cortesia(s) já foram enviadas para o seu e-mail. Mal podemos esperar para vê-lo no espetáculo!🌟 <br><br>";
        $bodyMail = "
        <b>Pedido Realizado com Sucesso!</b><br><br>
        ".($vTotal == 0 ? $free : $pay)."
        $ticketMail <br>
        $estacionamento
        <b>Dados: </b><br><br>
        Status: <b>" . ($vTotal == 0 ? "Pago </b><br>" : "Aguardando Pagamento </b><br>") . "
        Data do Pedido: " . date('H:i:s d-m-Y') . "<br>
        " . ($vTotal == 0 ? "" : "Forma de Pagamento: PIX <br>") . "
        Valor Total: R$$vTotal <br>
        Valido para: " . $form['periodo'];


        $this->sendMail("Carol Dance - Memórias", $bodyMail, [$form["email"]], [], ['vini15_silva@hotmail.com']);
        $this->database->commit();

        return $this->respondWithData(["cortesias" => $fTotal]);
    }

    private function validateForm(array $form): array {
        $this->validKeysForm($form, ['aluno', 'nome', 'cpf', 'email', 'periodo', 'assentos', 'estacionamento']);
        
        $form['cpf']      = $this->isCPF($form['cpf']);
        $form['email']    = $this->isEmail($form['email']);

        $limitTotalTickets         = 482;
        $limitTotalParking         = 100;
        $limitFreeTicketPerStudent = 2;

        $totalClientTickets     = count($form['assentos']);
        $totalPayTicketsClient  = array_count_values($form['assentos'])['40'] ?? 0;
        $totalFreeTicketsClient = array_count_values($form['assentos'])['0']  ?? 0;

        $student = $this->studentRepository->findStudentById((int)$form['aluno']);

        // Verifica se ultrapassou o limite dos ingressos e estacionamento
        $totalDBClientTickets  = $this->clientTicketRepository->findTotalClientTicketByPeriod($form['periodo']);
        $totalDBClientParking  = $this->clientTicketRepository->findTotalClientTicketByParking($form['periodo']);
        $sumTickets            = $totalDBClientTickets + $totalClientTickets;
        $sumParking            = $totalDBClientParking + (int)$form['estacionamento'];
        
        if($sumTickets > $limitTotalTickets){
            $remainsTickets = $limitTotalTickets - $totalDBClientTickets;
            $msg = $remainsTickets > 2 ? "Restam $remainsTickets ingressos disponíveis" : 'Nenhum ingresso disponível';
            throw new CustomDomainException("O limite máxmo de ingressos foi atingido. $msg!");
        }
        else if($totalFreeTicketsClient > $limitFreeTicketPerStudent){
            throw new CustomDomainException("São permitidos até $limitFreeTicketPerStudent ingressos cortesias por aluno!");
        }
        else if($sumParking > $limitTotalParking){
            throw new CustomDomainException("O limite máximo de vagas disponíveis do estacionamento foi atingido!");
        }

        // Verifica se o assento ja foi ocupado
        foreach($form['assentos'] as $k => $v){
            $ticket = $this->ticketRepository->findTicketById((int)$k);

            $seatName = $ticket->getAssento();
            if(!!$seats = $this->clientTicketRepository->findClientTicketBySeatId((int)$ticket->getId())){
                foreach($seats as $seat){
                    if($seat->getStatus() == 1 and $seat->getPeriodo() === $form['periodo']){
                        throw new CustomDomainException("O assento ".$seatName." não está disponível!");
                    }
                }
            }
        }

        if(!!$clientRepository = $this->clientTicketRepository->findClientTicketByStudentId($form['aluno'])){

            $quantityFreeTickets = 0;
            $quantityUsedParking = [];
            foreach($clientRepository as $v){
                if($v->getStatus() == 1){
                    if($v->getValor() == 0)
                        $quantityFreeTickets++;
                    if($form['estacionamento'] == '1'){
                        if($v->getEstacionamento() == 1 and !in_array($v->getPeriodo(), $quantityUsedParking))
                            array_push($quantityUsedParking, $v->getPeriodo());
                    }
                }
            }
            if($quantityFreeTickets + $totalFreeTicketsClient > $limitFreeTicketPerStudent){
                // if($student->getId() != 201 and date('d-m-Y') < '26-05-2024'){
                if(date('d-m-Y') < '23-11-2024'){
                    $avaible = $limitFreeTicketPerStudent - $quantityFreeTickets;
                    throw new CustomDomainException("São permitidos até $limitFreeTicketPerStudent ingressos cortesias por aluno! O mesmo possui $avaible ingresso(s) cortesia disponivel");
                }
            }
            else if(count($quantityUsedParking) > 0 and in_array($form['periodo'], $quantityUsedParking)){
                throw new CustomDomainException("Para esta Sessão, a vaga do estacionamento já foi solicitada!");
            }
            else {
                // if($form['aluno'] !== 203)
                $form = $this->unlockFreeTickets($limitFreeTicketPerStudent, $quantityFreeTickets + $totalFreeTicketsClient, $form);
            }
        }
        else {
            // Libera os gratuitos
            // if($form['aluno'] !== 203)
            $form = $this->unlockFreeTickets($limitFreeTicketPerStudent, $totalFreeTicketsClient, $form);
        }

        return $form;
    }

    private function unlockFreeTickets ($limitFreeTicketPerStudent, $totalFreeTicketsClient, $form){
        $c = $limitFreeTicketPerStudent - $totalFreeTicketsClient;
        foreach($form['assentos'] as $k => $v){
            if($c == 0) break;
            else if($v == 40){
                $form['assentos'][$k] = 0;
                $c--;
            }
        }
        return $form;
    }
}