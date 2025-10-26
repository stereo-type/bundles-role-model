<?php

/**
 * @package    DebugOpenApiAliasPass.php
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DebugOpenApiAliasPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Проверяем, существует ли сервис api_platform.openapi.factory
        if (!$container->hasDefinition('api_platform.openapi.factory') && !$container->hasAlias('api_platform.openapi.factory')) {
            return;
        }

        // Добавляем наш алиас только если сервис существует
        $container->setAlias(
            'app.public_open_api_factory',
            'api_platform.openapi.factory'
        )->setPublic(true); // делаем его публичным
    }
}
