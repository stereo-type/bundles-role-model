<?php

/**
 * @package    JWTExceptionListener.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JWTExceptionListener
{
    public const ERROR_CODE = Response::HTTP_UNAUTHORIZED;

    private function getResponse(string $message): JsonResponse
    {
        return new JsonResponse(
            [
                '@context' => '/api/contexts/Error',
                '@id' => '/api/errors/' . self::ERROR_CODE,
                '@type' => 'Error',
                'title' => 'Не авторизован',
                'detail' => $message,
                'error' => true,
                'code' => self::ERROR_CODE,
            ],
            self::ERROR_CODE
        );
    }

    /**
     * Слушатель на случай, если токен не найден
     */
    public function onJWTNotFound(JWTNotFoundEvent $event): void
    {
        $event->setResponse($this->getResponse('Необходима авторизация для доступа к этому ресурсу'));
    }

    /**
     * Слушатель на случай, если авторизация не удалась (например, неверный токен)
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $event->setResponse($this->getResponse('Ошибка авторизации'));
    }

    /**
     * Обработка невалидного JWT
     */
    public function onJWTNotInvalid(JWTInvalidEvent $event): void
    {
        $event->setResponse($this->getResponse('Некорректный токен авторизации'));

    }
    public function onJWTExpired(JWTExpiredEvent $event): void
    {
        $event->setResponse($this->getResponse('Токен авторизации устарел'));
    }
}
