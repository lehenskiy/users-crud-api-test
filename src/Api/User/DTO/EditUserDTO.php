<?php

declare(strict_types=1);

namespace App\Api\User\DTO;

use App\Api\Shared\DtoToConvertFromJsonInterface;
use App\Api\User\Gender;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Cascade]
readonly class EditUserDTO implements DtoToConvertFromJsonInterface
{
    #[Assert\Email]
    private ?string $email;

    #[Assert\Regex('/[A-Za-z]{3,}/', message: 'Username must contain at least 3 letters')]
    #[Assert\Length(min: 5, max: 20, minMessage: 'Username must contain at least 5 characters', maxMessage: 'Username must have 20 or less characters')]
    private ?string $username;

    #[Assert\Length(min: 7, minMessage: 'Your password should be at least 7 characters')]
    #[Assert\Expression('value === null || (value !== null && this.newPassword !== null)', 'Please enter your new password')]
    private ?string $oldPassword;

    #[Assert\Length(min: 7, minMessage: 'Your new password should be at least 7 characters')]
    #[Assert\NotIdenticalTo(propertyPath: 'oldPassword', message: 'New password cannot be the same as previous')]
    #[Assert\Expression('value === null || (value !== null && this.oldPassword !== null)', 'Please enter your previous password')]
    private ?string $newPassword;

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
        $this->oldPassword = $userData['oldPassword'] ?? null;
        $this->newPassword = $userData['newPassword'] ?? null;
        $this->gender = $userData['gender'] ?? null;
        $this->birthdate = $userData['birthdate'] ?? null;
        $this->country = $userData['country'] ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function getGender(): Gender|false|null
    {
        return match ($this->gender) {
            null => null,
            '' => false,
            'Male', 'male' => Gender::Male,
            'Female', 'female' => Gender::Female,
        };
    }

    public function getBirthdate(): DateTimeImmutable|false|null
    {
        return match ($this->birthdate) {
            null => null,
            '' => false,
            default => DateTimeImmutable::createFromFormat('Y-m-d', $this->birthdate),
        };
    }

    public function getCountry(): string|false|null
    {
        return match ($this->country) {
            null => null,
            '' => false,
            default => $this->country,
        };
    }
}
