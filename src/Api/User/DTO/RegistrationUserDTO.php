<?php

declare(strict_types=1);

namespace App\Api\User\DTO;

use App\Api\DtoToConvertFromJsonInterface;
use App\Api\User\Gender;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Cascade]
readonly class RegistrationUserDTO implements DtoToConvertFromJsonInterface
{
    #[Assert\Email]
    #[Assert\NotNull(message: 'Please provide email')]
    private ?string $email;

    #[Assert\Regex('/[A-Za-z]{3,}/', message: 'Username must contain at least 3 letters')]
    #[Assert\Length(min: 5, max: 20, minMessage: 'Username must contain at least 5 characters', maxMessage: 'Username must have 20 or less characters')]
    #[Assert\NotNull(message: 'Please provide username')]
    private ?string $username;

    #[Assert\Length(min: 7, minMessage: 'Your new password should be at least 7 characters')]
    #[Assert\NotNull(message: 'Please provide password')]

    private ?string $password;

    #[Assert\Choice(choices: [
        'female',
        'Female',
        'male',
        'Male',
        ''
    ], message: 'Gender must be either Male(male), Female(female) or \'\'(to unset)')]
    private ?string $gender;

    #[Assert\Date(message: 'Date must be in Y-m-d format')]
    private ?string $birthdate;

    #[Assert\Country]
    private ?string $country;

    public function __construct(array $userData)
    {
        $this->email = $userData['email'] ?? null;
        $this->username = $userData['username'] ?? null;
        $this->password = $userData['password'] ?? null;
        $this->gender = $userData['gender'] ?? null;
        $this->birthdate = $userData['birthdate'] ?? null;
        $this->country = $userData['country'] ?? null;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getGender(): ?Gender
    {
        if ($this->gender === null) {
            return null;
        } else {
            return lcfirst($this->gender) === 'male' ? Gender::Male : Gender::Female;
        }
    }

    public function getBirthdate(): ?DateTimeImmutable
    {
        if ($this->birthdate === null) {
            return null;
        } else {
            return DateTimeImmutable::createFromFormat('Y-m-d', $this->birthdate);
        }
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }
}
