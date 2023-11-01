<?php

declare(strict_types=1);

namespace App\Api\User;

use App\Api\User\DTO\EditUserDTO;
use App\Api\User\DTO\ListUsersDTO;
use App\Api\User\DTO\OutputUserDTO;
use App\Api\User\DTO\RegistrationUserDTO;
use DomainException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'user_api')]
class UserController extends AbstractController
{
    private const BAD_REQUEST_STATUS = 400;

    #[Route('/users', name: 'users_create_api', methods: ['post'])]
    #[ParamConverter(
        'registrationDTO',
        class: RegistrationUserDTO::class,
        options: ['validation' => 'dtoValidationMessages']
    )]
    public function createUser(
        RegistrationUserDTO $registrationDTO,
        UserService $service,
        array $dtoValidationMessages = []
    ): JsonResponse {
        if ($dtoValidationMessages !== []) {
            return $this->json(['message' => $dtoValidationMessages], self::BAD_REQUEST_STATUS);
        }

        try {
            $user = $service->registerUser($registrationDTO);
        } catch (DomainException $exception) {
            return $this->json(['message' => $exception->getMessage()], self::BAD_REQUEST_STATUS);
        }

        return $this->json(['message' => 'New user was created!', 'id' => $user->getId()]);
    }

    #[Route('/users', name: 'api_get_users', methods: ['get'])]
    public function findUsers(UserRepository $userRepository): JsonResponse
    {
        $usersList = new ListUsersDTO($userRepository->findAll());

        return $this->json($usersList->toArray());
    }

    #[Route('/user/{id}', name: 'api_get_user', methods: ['get'])]
    #[ParamConverter('user', class: 'App\Api\User\User')]
    public function findUser(User $user): JsonResponse
    {
        $usersData = (OutputUserDTO::FromUserEntity($user))->toArray();

        return $this->json($usersData);
    }

    #[Route('/user/{id}', name: 'api_update_user', requirements: ['id' => '\d+'], methods: ['patch'])]
    #[ParamConverter('user', class: User::class)]
    #[ParamConverter('editDTO', class: EditUserDTO::class, options: ['validation' => 'dtoValidationMessages'])]
    public function updateUser(
        EditUserDTO $editDTO,
        User $user,
        UserService $service,
        array $dtoValidationMessages = []
    ): JsonResponse {
        if ($dtoValidationMessages !== []) {
            return $this->json(['message' => $dtoValidationMessages], self::BAD_REQUEST_STATUS);
        }

        try {
            $user = $service->editUser($user, $editDTO);
        } catch (DomainException $exception) {
            return $this->json(['message' => $exception->getMessage()], self::BAD_REQUEST_STATUS);
        }

        return $this->json(['message' => 'User was updated successfully!', 'id' => $user->getId()]);
    }

    #[Route('/user/{id}', name: 'api_delete_user', requirements: ['id' => '\d+'], methods: ['delete'])]
    #[ParamConverter('user', class: User::class)]
    public function deleteUser(Request $request, User $user, UserService $service): JsonResponse
    {
        $decodedBody = json_decode($request->getContent(), true);
        if (empty($decodedBody)) {
            return $this->json(['message' => 'Please make sure data format is correct'], self::BAD_REQUEST_STATUS);
        }
        if (!isset($decodedBody['password'])) {
            return $this->json(['message' => 'Please provide password'], self::BAD_REQUEST_STATUS);
        }

        $deletedUserId = $user->getId();
        if ($service->deleteUser($user, $decodedBody['password'])) {
            return $this->json(['message' => 'User was deleted successfully!', 'id' => $deletedUserId]);
        } else {
            return $this->json(['message' => 'Password is incorrect']);
        }
    }
}
