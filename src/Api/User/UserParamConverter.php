<?php

declare(strict_types=1);

namespace App\Api\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserParamConverter extends DoctrineParamConverter
{
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        try {
            return parent::apply($request, $configuration);
        } catch (NotFoundHttpException $exception) {
            throw new NotFoundHttpException('User with this id does not exist');
        }
    }
}
