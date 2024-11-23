<?php

declare(strict_types=1);

namespace App\Application\Actions\ClientTicket;
use App\Domain\DomainException\CustomDomainException;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Psr\Http\Message\ResponseInterface as Response;

class ClientTicketConfirmPurchaseAction extends ClientTicketAction {

    protected function action(): Response {
        $form = $this->validateForm($this->post($this->request));

        $ticketMail = "<b>Ingressos: </b><br><br>";
        $vTotal     = 0;
        $vEstacionamento = 0;
        $estacionamento = "";
        $periodo = $form['periodo'];
        $qrcodes = [];
        $dataClient = "";

        foreach($form['assentos'] as $v){
            $seatName = $this->ticketRepository->findTicketById($v)->getAssento();
            if(!!$data = $this->database->select('*', 'cliente_ingresso', "ingresso_id = $v", "periodo = '{$form['periodo']}' AND status = 1")){

                $dataClient = $data;
                $this->database->update('cliente_ingresso', [
                    "status_pagamento" => "Concluido"
                ],
                "id = {$data[0]['id']}");
                switch((int)$data[0]['valor']){
                    case 0:
                        $ticketMail .= "Assento $seatName - Cortesia";
                        $ticketMail .= "<br>";
                        break;
                    default:
                        $vTotal += 40;
                        $ticketMail .= "Assento $seatName - R$ " . $data[0]['valor'];
                        $ticketMail .= "<br>";
                }
                $form['email'] = $data[0]['email'];
                if($data[0]['estacionamento'] == '1'){
                    $estacionamento = "Estacionamento: R$15<br><br>";
                    $vEstacionamento = 15;
                }                
                else if($data[0]['estacionamento'] == '2'){
                    $estacionamento = "Estacionamento: R$25<br><br>";
                    $vEstacionamento = 25;
                }

                // $baseUrl = "https://carol-dance-web.netlify.app/qrcode";
                // $url = $baseUrl . "?NOME=".urlencode($data[0]['nome'])."&CPF=".urlencode($data[0]['cpf'])."&ASSENTOS=".urlencode($seatName)."&SESSSAO=".urlencode($periodo);
                // $builder = new Builder(
                //     new PngWriter(),
                //     [],
                //     false,
                //     $url,
                //     new Encoding('UTF-8'),
                //     ErrorCorrectionLevel::High,
                //     300,
                //     10,
                //     RoundBlockSizeMode::Margin,
                //     new Color(0,0,0),
                //     new Color(255,255,255),
                //     "Assento $seatName - $periodo",
                //     new OpenSans(13),
                //     LabelAlignment::Center  
                // );
                // $result = $builder->build();    
                // $qrcodes[] = ["data" => $result->getString(), "name" => $periodo . '.png', "typeMIME" => 'base64', "typeImage" => "image/png"];
                $payload = [
                    "nome" => $data[0]['nome'],
                    "cpf"  => $data[0]['cpf'],
                    "seatName" => $seatName,
                    "periodo"  => $periodo,
                    "nomeIngresso" => "Assento $seatName - $periodo"
                ];
                array_push($qrcodes, self::generateQRCODE($payload));
            }
            else{
                throw new CustomDomainException("O assento $seatName não foi localizado na base de dados!");
            }
        }
        $payload = [
            "nome" => $data[0]['nome'],
            "cpf"  => $data[0]['cpf'],
            "seatName" => $seatName,
            "periodo"  => "",
            "nomeIngresso" => ""
        ];
        if ($dataClient[0]['estacionamento'] == 1){
            $payload['periodo'] = $periodo;
            $payload['nomeIngresso'] = "Estacionamento - $periodo";
            array_push($qrcodes, self::generateQRCODE($payload));

        }
        else if ($dataClient[0]['estacionamento'] == 2){
            $payload['periodo'] = "11/12/2024 - SESSAO 1 e 2";
            $payload['nomeIngresso'] = "Estacionamento - 11/12/2024 SESSÕES 1 e 2";
            array_push($qrcodes, self::generateQRCODE($payload, 11));
        }

        $vTotal += $vEstacionamento;
        $bodyMail = "
        <b>Pedido Realizado com Sucesso!</b><br><br>
        Recebemos seu pagamento com sucesso! Abaixo estão os dados da sua compra.<br><br>
        $ticketMail <br>
        $estacionamento
        <b>Dados: </b><br><br>
        Status: <b>Concluído</b><br>
        Data do Pedido: " . date('H:i:s d-m-Y') . "<br>
        Forma de Pagamento: PIX <br>
        Valor Total: $vTotal <br>
        Sessão: " . explode('SESSAO', $form['periodo'])[1];

        $this->sendMail("Carol Dance - O verdadeiro presente de natal!", $bodyMail, [$form["email"]], [], ['vini15_silva@hotmail.com', 'biabarros10@icloud.com'], false, [], false, '', true, $qrcodes);

        $this->database->commit();

        return $this->respondWithData();
    }

    private function validateForm(array $form): array {
        $this->validKeysForm($form, ['periodo', 'assentos']);

        foreach($form['assentos'] as $k =>$v){
            if(!!$seat = $this->ticketRepository->findTicketBySeat(strtoupper($v))){
                $form['assentos'][$k] = $seat->getId();
            }
            else {
                throw new CustomDomainException("O assento $v não foi localizado!");
            }
        }

        return $form;
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