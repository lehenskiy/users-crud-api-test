<?php

declare(strict_types=1);

namespace App\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JsonToDtoParamConverter implements ParamConverterInterface
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function apply(Request $request, ParamConverter $configuration): void
    {
        $decodedBody = json_decode($request->getContent(), true);
        if (empty($decodedBody)) {
            throw new BadRequestHttpException('Invalid body format');
        }

        $dto = new ($configuration->getClass())($decodedBody);

        $errors = $this->validator->validate($dto);
        if (
            isset($configuration->getOptions()['validation'])
            && $configuration->getOptions()['validation'] !== false
            && count($errors) > 0
        ) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            $request->attributes->set($configuration->getOptions()['validation'], $errorMessages);
        }

        $request->attributes->set($configuration->getName(), $dto);
    }

    public function supports(ParamConverter $configuration): bool
    {
        return is_a($configuration->getClass(), DtoToConvertFromJsonInterface::class, true);
    }
}
