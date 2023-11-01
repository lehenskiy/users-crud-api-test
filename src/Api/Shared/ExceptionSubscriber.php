<?php

namespace App\Api\Shared;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private const BAD_REQUEST_STATUS = 400;
    private const NOT_FOUND_STATUS = 404;
    private const METHOD_NOT_ALLOWED_STATUS = 405;

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (
            $exception instanceof HttpExceptionInterface
            && $event->getRequest()->headers->get('Content-Type') === 'application/json'
        ) {
            if (in_array($exception->getStatusCode(), [self::BAD_REQUEST_STATUS, self::NOT_FOUND_STATUS])) {
                $response = new JsonResponse(['message' => $exception->getMessage()], $exception->getStatusCode());

                $event->setResponse($response);
            } elseif ($exception->getStatusCode() === self::METHOD_NOT_ALLOWED_STATUS) {
                $response = new JsonResponse(['message' => 'Incorrect method or URL'], self::METHOD_NOT_ALLOWED_STATUS);

                $event->setResponse($response);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
