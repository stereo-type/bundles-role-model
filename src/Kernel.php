<?php

namespace Slcorp\RoleModelBundle;

use Slcorp\RoleModelBundle\DependencyInjection\SlcorpRoleModelExtension;
use Slcorp\RoleModelBundle\DependencyInjection\DebugOpenApiAliasPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $extension = new SlcorpRoleModelExtension();
        $extension->setLocalLaunch();
        $container->registerExtension($extension);
        $container->loadFromExtension($extension->getAlias());
        $container->addCompilerPass(new DebugOpenApiAliasPass());
    }
}
