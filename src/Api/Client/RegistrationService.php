<?php

declare(strict_types=1);

namespace App\Api\Client;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private ClientRepository $clientRepository
    ) {
    }

    public function register(ClientDTO $clientData): bool
    {
        $client = new Client();
        $hashedPassword = $this->passwordHasher->hashPassword($client, $clientData->password);

        $this->setClientData($client, $clientData->identifier, $hashedPassword);
        try {
            $this->clientRepository->save($client, true);
        } catch (UniqueConstraintViolationException) {
            return false;
        }

        return true;
    }

    private function setClientData(Client $client, string $identifier, string $hashedPassword): void
    {
        $client->setPassword($hashedPassword);
        $client->setIdentifier($identifier);
    }
}
