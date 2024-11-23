<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;
use App\Domain\DomainException\CustomDomainException;
use App\Domain\ParkingTicket\ParkingTicket;
use Psr\Http\Message\ResponseInterface as Response;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class ClientTicketConfirmPurchaseParkingAction extends ClientTicketAction {

    protected function action(): Response {
        $client = $this->validateForm($this->post($this->request));

        $this->database->update('estacionamento_ingresso', [
            "status_pagamento" => 'Concluido'
        ], 
        "id = " . $client->getId());

        $bodyMail = "
        <b>Pedido Realizado com Sucesso!</b><br><br>
        Recebemos seu pagamento com sucesso! Abaixo estão os dados da sua compra.<br><br>
        <b>Item:</b> <br><br>
        Estacionamento - R$15 <br><br>
        <b>Dados: </b><br><br>
        Status: <b>Concluido </b><br>
        Data do Pedido: " . $client->getDataInclusao() . "<br>
        Forma de Pagamento: PIX <br>
        Valor Total: R$15 <br>
        Valido para: " . $client->getPeriodo();

        $payload = [
            "nome" => $client->getNome(),
            "cpf"  => $client->getCpf(),
            "seatName" => "",
            "periodo"  => $client->getPeriodo(),
            "nomeIngresso" => "Estacionamento - " . $client->getPeriodo()
        ];
        $qrcodes[] = self::generateQRCODE($payload, 12);
        // $this->sendMail("Carol Dance - O verdadeiro presente de natal!", $bodyMail, [$client->getEmail()], [], ['vini15_silva@hotmail.com']);
        $this->sendMail("Carol Dance - O verdadeiro presente de natal!", $bodyMail, [$client->getEmail()], [], ['vini15_silva@hotmail.com', 'biabarros10@icloud.com'], false, [], false, '', true, $qrcodes);
        $this->database->commit();

        return $this->respondWithData();
    }

    private function validateForm(array $form): ParkingTicket {
        $this->validKeysForm($form, ['id']);

        $parking = $this->parkingTicketRepository->findParkingTicketById($form['id']);

        if($parking->getStatusPagamento() == 'Concluido')
            throw new CustomDomainException('O pagamento já foi concluido!');
        
        return $parking;
    }
    private function generateQRCODE($data, $lengthTextQRCode=13){
        $baseUrl = "https://carol-dance-web.netlify.app/qrcode";
        $url = $baseUrl . "?NOME=".urlencode($data['nome'])."&CPF=".urlencode($data['cpf'])."&ASSENTOS=".urlencode($data['seatName'])."&SESSSAO=".urlencode($data['periodo']);
        $builder = new Builder(
            new PngWriter(),
            [],
            false,
            $url,
            new Encoding('UTF-8'),
            ErrorCorrectionLevel::High,
            300,
            10,
            RoundBlockSizeMode::Margin,
            new Color(0,0,0),
            new Color(255,255,255),
            $data["nomeIngresso"],
            new OpenSans($lengthTextQRCode),
            LabelAlignment::Center 
        );
        $result = $builder->build();    
        return ["data" => $result->getString(), "name" => $data['periodo'] . '.png', "typeMIME" => 'base64', "typeImage" => "image/png"];
    }
}