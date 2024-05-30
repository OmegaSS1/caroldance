<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;
use App\Domain\DomainException\CustomDomainException;
use Psr\Http\Message\ResponseInterface as Response;

class ClientTicketPurchaseParkingAction extends ClientTicketAction {

    protected function action(): Response {
        $form = $this->validateForm($this->post($this->request));

        $this->database->update('cliente_ingresso', [
            "estacionamento" => 1,
        ], 
        "aluno_id = {$form['aluno']}", "periodo = '{$form['periodo']}' AND status = 1");

        $bodyMail = "
        <b>Pedido Realizado com Sucesso!</b><br><br>
        Recebemos seu pedido com sucesso, estamos aguardando o pagamento via pix e o envio do comprovante via WhatsApp: (71) 98690-4826<br><br>
        Status: Aguardando Pagamento </b><br>
        Data do Pedido: " . date('H:i:s d-m-Y') . "<br>
        Forma de Pagamento: PIX <br>
        Valor Total: R$15 <br>
        Valido para: " . $form['periodo'];


        $this->sendMail("Carol Dance - Memórias", $bodyMail, [$form["email"]], [], ['vini15_silva@hotmail.com']);
        // $this->database->commit();

        return $this->respondWithData();
    }

    private function validateForm(array $form): array {
        $this->validKeysForm($form, ['aluno', 'periodo', 'cpf', 'nome', 'email']);
        $this->isCPF($form['cpf']);
        $this->isEmail($form['email']);

        $this->studentRepository->findStudentById((int)$form['aluno']);

        if(!!$data = $this->clientTicketRepository->findAll()){
            foreach($data as $client){
                if($client->getAlunoId() == (int)$form['aluno'] and $client->getStatus() == 1 and $client->getPeriodo() == $form['periodo']){
                    if($client->getCpf() != $form['cpf'])
                        throw new CustomDomainException('O CPF informado é diferente do registrado no pedido dos assentos!');
                    else if($client->getNome() != $form['nome'])
                        throw new CustomDomainException('O Nome do comprador é diferente do registrado no pedido dos assentos!');
                    else if($client->getEstacionamento() == 1){
                        if($client->getStatusPagamento() == 'Pendente')
                            throw new CustomDomainException('Para este aluno, existe um pedido de estacionamento pendente nesta sessão.');
                        else
                            throw new CustomDomainException("Para este aluno, o estacionamento já foi adquirido.");
                    }
                    else    
                        break;
                }
                else 
                    throw new CustomDomainException('Nenhum pedido foi encontrado com os dados informados!');
            }
        }
        else 
            throw new CustomDomainException("Nenhum dado encontrado.");
        

        return $form;
    }
}