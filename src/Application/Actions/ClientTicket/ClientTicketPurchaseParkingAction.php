<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;
use App\Domain\DomainException\CustomDomainException;
use Psr\Http\Message\ResponseInterface as Response;

class ClientTicketPurchaseParkingAction extends ClientTicketAction {

    protected function action(): Response {
        $form = $this->validateForm($this->post($this->request));
        switch($form['estacionamento']){
            case '1':
                $value = 15;
                $periodo = $form['periodo'];
                break;
            case '2':
                $value = 25;
                $periodo = "11/12/2024 - SESSÕES 1 E 2";
                break;

            default:
                $value = 0;
                $periodo = "";
        }

        $this->database->insert('estacionamento_ingresso', [
            "aluno_id" => $form['aluno'],
            "periodo"  => $form['periodo'],
            "nome" => $form['nome'],
            "cpf" => $form['cpf'],
            "email" => $form['email'],
            "valor" => $value
        ]);

        
        $bodyMail = "
        <b>Pedido Realizado com Sucesso!</b><br><br>
        Recebemos seu pedido com sucesso, estamos aguardando o pagamento via pix e o envio do comprovante via WhatsApp: (71) 98690-4826<br><br>
        <b>Item:</b> <br><br>
        Estacionamento - R$$value <br><br>
        <b>Dados: </b><br><br>
        Status: <b>Aguardando Pagamento </b><br>
        Data do Pedido: " . date('d/m/Y H:i:s') . "<br>
        Forma de Pagamento: PIX <br>
        Valor Total: R$$value <br>
        Valido para: $periodo";


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
                if($client->getAlunoId() != (int)$form['aluno'])
                    throw new CustomDomainException("Nenhum aluno foi localizado com os dados informados!");

                else if($client->getStatus() != 1)
                    throw new CustomDomainException("Nenhum pedido foi encontrado com os dados informados!");

                else if($client->getCpf() != $form['cpf'])   
                    throw new CustomDomainException("Nenhum pedido foi encontrado com o cpf informado!");
                
                else if($form['estacionamento'] == 1 and $client->getPeriodo() != $form['periodo'])
                    throw new CustomDomainException("Nenhum pedido foi encontrado com a sessão informada!");
             
                else if ($form['estacionamento'] == 1 or $form['estacionamento'] == 2){
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
                    break;
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