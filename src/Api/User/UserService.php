<?php

declare(strict_types=1);

namespace App\Api\User;

use App\Api\User\DTO\EditUserDTO;
use App\Api\User\DTO\RegistrationUserDTO;
use App\Api\User\Exception\CurrentPasswordNotValidException;
use App\Api\User\Exception\UserAlreadyExistsException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class UserService
{
    private PasswordHasherInterface $passwordHasher;

    public function __construct(
        PasswordHasherFactoryInterface $passwordHasherFactory,
        private UserRepository $userRepository
    ) {
        $this->passwordHasher = $passwordHasherFactory->getPasswordHasher(User::class);
    }

    public function registerUser(RegistrationUserDTO $userData): User
    {
        $user = new User();

        $user->setEmail($userData->getEmail());
        $user->setUsername($userData->getUsername());
        $user->setPassword($this->passwordHasher->hash($userData->getPassword()));
        $user->setGender($userData->getGender());
        $user->setBirthdate($userData->getBirthdate());
        $user->setCountry($userData->getCountry());

        return $this->saveUser($user);
    }

    public function editUser(User $user, EditUserDTO $userData): User
    {
        if (
            $userData->getOldPassword() !== null
            && !$this->passwordHasher->verify($user->getPassword(), $userData->getOldPassword())
        ) {
            throw new CurrentPasswordNotValidException('Old password is not correct');
        }

        if ($userData->getEmail() !== null) {
            $user->setEmail($userData->getEmail());
        }
        if ($userData->getUsername() !== null) {
            $user->setUsername($userData->getUsername());
        }
        if ($userData->getNewPassword() !== null) {
            $user->setPassword($this->passwordHasher->hash($userData->getNewPassword()));
        }
        if ($userData->getGender() !== null) {
            if (!$userData->getGender()) {
                $user->setGender(null);
            } else {
                $user->setGender($userData->getGender());
            }
        }
        if ($userData->getBirthdate() !== null) {
            if (!$userData->getBirthdate()) {
                $user->setBirthdate(null);
            } else {
                $user->setBirthdate($userData->getBirthdate());
            }
        }
        if ($userData->getCountry() !== null) {
            $user->setCountry($userData->getCountry() === false ? null : $userData->getCountry());
        }

        return $this->saveUser($user);
    }

    public function deleteUser(User $user, string $password): bool
    {
        if ($this->passwordHasher->verify($user->getPassword(), $password)) {
            $this->userRepository->remove($user, true);

            return true;
        } else {
            return false;
        }
    }

    private function saveUser(User $user): User
    {
        try {
            $this->userRepository->save($user, true);
        } catch (UniqueConstraintViolationException $exception) {
            throw new UserAlreadyExistsException(
                'User with such email already exists',
                previous: $exception
            );
        }

        return $user;
    }
}
