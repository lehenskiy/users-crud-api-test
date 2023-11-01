<?php

declare(strict_types=1);

namespace App\Api\User\DTO;

use App\Api\User\User;

readonly class OutputUserDTO
{
    public int $id;
    public string $email;
    public string $username;
    public string $registrationTime;
    public string $gender;
    public string $birthdate;
    public string $country;

    public function __construct(array $userData)
    {
        $this->id = $userData['id'];
        $this->email = $userData['email'];
        $this->username = $userData['username'];
        $this->registrationTime = $userData['registrationTime']->format('d.m.Y H:i');
        $this->gender = $userData['gender']?->name ?? 'Not set';
        $this->birthdate = $userData['birthdate'] === null ? 'Not set' : $userData['birthdate']->format('d.m.Y');
        $this->country = $userData['country'] ?? 'Not set';
    }

    public static function FromUserEntity(User $user): self
    {
        return new self([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'registrationTime' => $user->getCreatedAt(),
            'gender' => $user->getGender() ?? null,
            'birthdate' => $user->getBirthdate() ?? null,
            'country' => $user->getCountry() ?? null,
        ]);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'registrationTime' => $this->registrationTime,
            'gender' => $this->gender,
            'birthdate' => $this->birthdate,
            'country' => $this->country,
        ];
    }
}
