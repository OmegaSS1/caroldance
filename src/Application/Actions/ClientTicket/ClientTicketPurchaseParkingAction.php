<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;
use App\Domain\DomainException\CustomDomainException;
use Psr\Http\Message\ResponseInterface as Response;

class ClientTicketPurchaseParkingAction extends ClientTicketAction {

    protected function action(): Response {
        $form = $this->validateForm($this->post($this->request));

        $this->database->insert('estacionamento_ingresso', [
            "aluno_id" => $form['aluno'],
            "periodo"  => $form['periodo'],
            "nome" => $form['nome'],
            "cpf" => $form['cpf'],
            "email" => $form['email'],
            "valor" => 15
        ]);

        $bodyMail = "
        <b>Pedido Realizado com Sucesso!</b><br><br>
        Recebemos seu pedido com sucesso, estamos aguardando o pagamento via pix e o envio do comprovante via WhatsApp: (71) 98690-4826<br><br>
        <b>Item:</b> <br><br>
        Estacionamento - R$15 <br><br>
        <b>Dados: </b><br><br>
        Status: <b>Aguardando Pagamento </b><br>
        Data do Pedido: " . date('H:i:s d-m-Y') . "<br>
        Forma de Pagamento: PIX <br>
        Valor Total: R$15 <br>
        Valido para: " . $form['periodo'];


        $this->sendMail("Carol Dance - O verdadeiro presente de natal!", $bodyMail, [$form['email']], [], ['vini15_silva@hotmail.com']);
        $this->database->commit();

        return $this->respondWithData();
    }

    private function validateForm(array $form): array {
        $this->validKeysForm($form, ['aluno', 'periodo', 'cpf', 'nome', 'email']);
        $this->isCPF($form['cpf']);
        $this->isEmail($form['email']);

        $this->studentRepository->findStudentById((int)$form['aluno']);

        $hasClient         = false;
        $limitTotalParking = 80;

        if(!!$data = $this->clientTicketRepository->findClientTicketByStudentId($form['aluno'])){
            foreach($data as $client){
                if($client->getAlunoId() == (int)$form['aluno'] and $client->getStatus() == 1 and $client->getPeriodo() == $form['periodo']){
                    if($client->getCpf() == $form['cpf']){
                        // if($client->getEstacionamento() == 1){
                        //     if($client->getStatusPagamento() == 'Pendente')
                        //         throw new CustomDomainException('Para este aluno, existe um pedido de estacionamento pendente nesta sessão.');
                        //     else
                        //         throw new CustomDomainException("Para este aluno, o estacionamento já foi adquirido nesta sessão.");
                        // }
                        // else if(!!$parking = $this->parkingTicketRepository->findParkingTicketByStudentIdAndPeriod($form['aluno'], $form['periodo'])){
                        //     if($parking->getStatusPagamento() == 'Pendente')
                        //         throw new CustomDomainException('Para este aluno, existe um pedido de estacionamento pendente nesta sessão!');
                        //     else if($parking->getStatusPagamento() == 'Concluido')
                        //         throw new CustomDomainException('Para este aluno, o estacionamento já foi adquirido nesta sessão.');
                        //     else 
                        //         $hasClient = true;
                            
                        // }
                        // else{
                            // $hasClient = true;
                        // }
                        $hasClient = true;
                    }
                }
            }
        }
        else 
            throw new CustomDomainException("Nenhum pedido foi encontrado com os dados informados!");

        if(!$hasClient)
            throw new CustomDomainException('Nenhum pedido foi encontrado com os dados informados!');

        $totalDBClientParking  = $this->clientTicketRepository->findTotalClientTicketByParking($form['periodo']);
        $sumParking            = $totalDBClientParking + 1;
        
        if($sumParking > $limitTotalParking){
            throw new CustomDomainException("O limite máximo de vagas disponíveis do estacionamento foi atingido!");
        }
        return $form;
    }
}