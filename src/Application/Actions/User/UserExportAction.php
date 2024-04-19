<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;
use DateTimeImmutable;

class UserExportAction extends UserAction
{

    protected function action(): Response
    {
        $users = $this->userRepository->findAll();

        $data[] = ['NOME', 'WHATSAPP', 'CPF', 'DATA DE NASCIMENTO', 'EMAIL', 'PERFIL', 'STATUS'];

        foreach ($users as $user) {
            $nome           = ucfirst($user->getNome()) . ' ' . ucfirst($user->getSobrenome());
            $whatsapp       = $user->getTelefoneWhatsapp();
            $cpf            = $user->getCpf();
            $dataNascimento = DateTimeImmutable::createFromFormat('Y-m-d', $user->getDataNascimento())->format('d/m/Y');
            $email          = $user->getEmail();
            $perfil         = $this->profileUserRepository->findProfileUserById($user->getPerfilUsuarioId())->getNome();
            $status         = !$user->getStatus() ? 'Inativo' : 'Ativo';

            $data[] = [
                "nome"           => $nome,
                "whatsapp"       => $whatsapp,
                "cpf"            => $cpf,
                "dataNascimento" => $dataNascimento,
                "email"          => $email,
                "perfil"         => $perfil,
                "status"         => $status
            ];
        }

        $filename = 'Relatorio de usuarios ' . date('H:i:s d-m-Y');
        return $this->BoxSpout($data, $filename, $this->args['extension'], $this->respondWithData(), 'D');

    }
}