<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle;

use Slcorp\RoleModelBundle\DependencyInjection\SlcorpRoleModelExtension;
use Slcorp\RoleModelBundle\DependencyInjection\DebugOpenApiAliasPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SlcorpRoleModelBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new DebugOpenApiAliasPass());
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new SlcorpRoleModelExtension();
    }


}
