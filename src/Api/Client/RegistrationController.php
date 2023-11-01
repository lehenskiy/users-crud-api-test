<?php

declare(strict_types=1);

namespace App\Api\Client;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private const BAD_REQUEST_STATUS = 400;

    #[Route('/api/signup', name: 'api_client_registration', methods: ['post'])]
    #[ParamConverter('clientDTO', class: ClientDTO::class, options: ['validation' => 'dtoValidationMessages'])]
    public function index(
        ClientDTO $clientDTO,
        RegistrationService $service,
        array $dtoValidationMessages = []
    ): JsonResponse {
        if ($dtoValidationMessages !== []) {
            return $this->json(['message' => $dtoValidationMessages], self::BAD_REQUEST_STATUS);
        }

        if (!$service->register($clientDTO)) {
            return $this->json(['message' => 'Client with this identifier already exists']);
        } else {
            return $this->json(['message' => 'New client registered successfully!']);
        }
    }
}
