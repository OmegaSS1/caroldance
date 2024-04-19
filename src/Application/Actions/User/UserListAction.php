<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;
use DateTimeImmutable;

class UserListAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            $nome           = ucfirst($user->getNome()) . ' ' . ucfirst($user->getSobrenome());
            $whatsapp       = strlen($user->getTelefoneWhatsapp()) == 11 
                              ? '('. substr($user->getTelefoneWhatsapp(), 0, 2) . ')' . substr($user->getTelefoneWhatsapp(), 2, 5) .'-'. substr($user->getTelefoneWhatsapp(), 7, 9)
                              : '('. substr($user->getTelefoneWhatsapp(), 0, 2) . ')' . substr($user->getTelefoneWhatsapp(), 2, 5) .'-'. substr($user->getTelefoneWhatsapp(), 7, 9);
            $cpf            = substr($user->getCpf(), 0, 3) . '.' . substr($user->getCpf(), 3, 3) . '.' . substr($user->getCpf(), 6, 3) . '-' . substr($user->getCpf(), 9, 2);
            $dataNascimento = DateTimeImmutable::createFromFormat('Y-m-d', $user->getDataNascimento())->format('d/m/Y');
            $email          = strtolower($user->getEmail());
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

        return $this->respondWithData($data ?? []);
    }
}
