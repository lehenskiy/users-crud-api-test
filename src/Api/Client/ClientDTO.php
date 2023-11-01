<?php

declare(strict_types=1);

namespace App\Api\Client;

use App\Api\DtoToConvertFromJsonInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ClientDTO implements DtoToConvertFromJsonInterface
{
    #[Assert\Length(min: 5, max: 180, minMessage: 'Identifier must contain at least 5 characters', maxMessage: 'Identifier must have 180 or less characters')]
    #[Assert\NotNull(message: 'Please provide identifier(email, username, application name, company name)')]
    public ?string $identifier;

    #[Assert\Length(min: 7, minMessage: 'Your password should be at least 7 characters')]
    #[Assert\NotNull(message: 'Please provide password')]
    public ?string $password;

    public function __construct(array $clientData)
    {
        $this->identifier = $clientData['identifier'] ?? null;
        $this->password = $clientData['password'] ?? null;
    }
}
