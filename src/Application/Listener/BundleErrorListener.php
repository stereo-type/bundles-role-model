<?php

/**
 * @package    NoFoundErrorLisnter.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Listener;

use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class BundleErrorListener
{
    public function onError(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof BundleException) {
            $data = $exception->getExceptionData();
            $code = $data->getStatusCode();
            $payload = [
                '@context' => '/api/contexts/Error',
                '@id' => '/api/errors/' . $code,
                '@type' => $data->getType()->value,
                'title' => $data->getTitle(),
                'detail' => $data->getMessage(),
                'description' => $data->getMessage(),
                'status' => $code,
                'type' => '/errors/' . $code,
                'data' => $data->toArray()
            ];

            $response = new JsonResponse($payload, $code);
            $event->setResponse($response);
        }
    }
}
