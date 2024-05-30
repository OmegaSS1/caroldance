<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;
use App\Domain\DomainException\CustomDomainException;
use Psr\Http\Message\ResponseInterface as Response;

class Teste  extends ClientTicketAction{

    protected function action(): Response {
        $form = $this->validateForm($this->post($this->request));

        $ticketMail = "<b>Ingressos: </b><br><br>";
        foreach($form['assentos'] as $seat){
            $ticketMail .= "Assento $seat - Cortesia";
            $ticketMail .= "<br>";
        }

        $bodyMail = "
        <b>Pedido Realizado com Sucesso!</b><br><br>
        Recebemos seu pedido com sucesso! 🎉 Faça o download do QRCODE das suas cortesias (em anexo). Mal podemos esperar para vê-lo no espetáculo!🌟
        $ticketMail <br>
        <b>Dados: </b><br><br>
        Status: <b>Concluído</b><br>
        Data do Pedido: {$form['dataPedido']} <br>
        Valor Total: R$0 <br>
        Sessão: " . explode('SESSAO', $form['periodo'])[1];

        $decodeBase64 = base64_decode(str_replace('data:image/png;base64,', '', $form['qrcode']));
        if ($decodeBase64 === false) {
            throw new CustomDomainException('Decodificação base64 falhou');
        }

        $periodo = str_replace('/', '-', $form['periodo']);

        // $this->sendMail("Carol Dance - Memórias", $bodyMail, [$form["email"]], [], [], false, [], false, '', true, [["data" => $decodeBase64, "name" => $periodo . '.png', "typeMIME" => 'base64', "typeImage" => "image/png"]]);
        // $this->sendMail("Carol Dance - Memórias", $bodyMail, ['vini15_silva@hotmail.com'], [], [], false, [], false, '', true, [["data" => $decodeBase64, "name" => $periodo . '.png', "typeMIME" => 'base64', "typeImage" => "image/png"]]);

        return $this->respondWithData();
    }

    private function validateForm(array $form): array {
        $this->validKeysForm($form, ['aluno', 'nome', 'cpf', 'email', 'periodo', 'assentos', 'estacionamento']);
        
        $form['cpf']      = $this->isCPF($form['cpf']);
        $form['email']    = $this->isEmail($form['email']);

        $limitTotalTickets         = 482;
        $limitTotalParking         = 100;
        $limitFreeTicketPerStudent = 2;

        $totalClientTickets     = count($form['assentos']);
        $totalPayTicketsClient  = array_count_values($form['assentos'])['30'] ?? 0;
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
                if($student->getId() != 201 and date('d-m-Y') < '26-05-2024'){
                    $avaible = $limitFreeTicketPerStudent - $quantityFreeTickets;
                    throw new CustomDomainException("São permitidos até $limitFreeTicketPerStudent ingressos cortesias por aluno! O mesmo possui $avaible ingresso(s) cortesia disponivel");
                }
            }
            else if(count($quantityUsedParking) > 0 and in_array($form['periodo'], $quantityUsedParking)){
                throw new CustomDomainException("Para esta Sessão, a vaga do estacionamento já foi solicitada!");
            }
            else {
                if($form['aluno'] != 203)
                    $form = $this->unlockFreeTickets($limitFreeTicketPerStudent, $quantityFreeTickets + $totalFreeTicketsClient, $form);
            }
        }
        else {
            // Libera os gratuitos
            if($form['aluno'] != 203)
                $form = $this->unlockFreeTickets($limitFreeTicketPerStudent, $totalFreeTicketsClient, $form);
        }

        return $form;
    }

    private function unlockFreeTickets ($limitFreeTicketPerStudent, $totalFreeTicketsClient, $form){
        $c = $limitFreeTicketPerStudent - $totalFreeTicketsClient;
        foreach($form['assentos'] as $k => $v){
            if($c == 0) break;
            else if($v == 30){
                $form['assentos'][$k] = 0;
                $c--;
            }
        }
        return $form;
    }
}