<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;

use Psr\Http\Message\ResponseInterface as Response;

class ClientTicketListParkingAction extends ClientTicketAction {

    public function action(): Response {

        foreach($this->parkingTicketRepository->findAll() as $k => $parking){
            $student = $this->studentRepository->findStudentById($parking->getAlunoId())->getNome();

            $data[] = [
                'id' => $parking->getId(),
                'aluno' => $student,
                'periodo' => $parking->getPeriodo(),
                'nome' => $parking->getNome(),
                'cpf' => $parking->getCpf(),
                'email' => $parking->getEmail(),
                'valor' => $parking->getValor(),
                'statusPagamento' => $parking->getStatusPagamento(),
                'data_atualizacao' => \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $parking->getDataAtualizacao())->format('d/m/Y H:i:s')    
            ];
        }
        return $this->respondWithData($data ?? []);
    }

}