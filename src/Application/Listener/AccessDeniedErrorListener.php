<?php

/**
 * @package    NoFoundErrorLisnter.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Listener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AccessDeniedErrorListener
{
    private const ERROR_CODE = Response::HTTP_FORBIDDEN;

    public function onAccessDenied(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Проверяем, является ли это исключение AccessDeniedHttpException
        if ($exception instanceof AccessDeniedHttpException) {
            $response = new JsonResponse([
                '@context' => '/api/contexts/Error',
                '@id' => '/api/errors/' . self::ERROR_CODE,
                '@type' => 'Error',
                'title' => 'Доступ запрещен',
                'message' => $exception->getMessage(),
                'detail' => 'У вас недостаточно прав для доступа к этому ресурсу.',
                'status' => self::ERROR_CODE,
                'type' => '/errors/' . self::ERROR_CODE,
            ], self::ERROR_CODE);

            $event->setResponse($response);
        }
    }
}
