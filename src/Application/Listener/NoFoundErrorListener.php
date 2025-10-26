<?php

/**
 * @package    NoFoundErrorLisnter.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Listener;

use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RequestStack;

class NoFoundErrorListener implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $this->requestStack->getCurrentRequest();

        // Если это NotFound ошибка, мы можем выбросить наше кастомное исключение
        if ($request && $exception instanceof NotFoundHttpException) {
            // Получаем параметры запроса и другие данные
            $params = $request->attributes->all(); // Параметры запроса
            $entityId = (int)($params['id'] ?? -1); // Пример, если вы ищете по ID
            $entityClass = $params['_api_resource_class'] ?? null;
            if ($entityClass) {
                $array = explode('\\', $entityClass);
                $entityClassName = strtolower(end($array));
                $errorMethod = $entityClassName.'NotFound';
                if (method_exists(BundleException::class, $errorMethod)) {
                    throw BundleException::$errorMethod($entityId);
                }
            }

            throw $exception;
        }
    }

}
