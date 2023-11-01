<?php

declare(strict_types=1);

namespace App\Api\User\DTO;

readonly class ListUsersDTO
{
    public function __construct(public array $users)
    {
    }

    public function toArray(): array
    {
        $usersData = [];

        foreach ($this->users as $user) {
            $usersData[] = (OutputUserDTO::FromUserEntity($user))->toArray();
        }

        return $usersData;
    }
}
