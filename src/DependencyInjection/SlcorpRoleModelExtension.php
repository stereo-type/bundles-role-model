<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\DependencyInjection;

use Exception;
use InvalidArgumentException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Filesystem\Filesystem;

class SlcorpRoleModelExtension extends Extension implements PrependExtensionInterface
{
    private const PERMISSIONS_MASK = 0755;

    private Filesystem $filesystem;
    private string $projectRoot;

    private bool $bundleLaunch = true;

    public function setLocalLaunch(): void
    {
        $this->bundleLaunch = false;
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        if ($config['use_gid'] && !$config['secret_key']) {
            throw new InvalidArgumentException(
                'The "secret_key" must be configured when "use_gid" is set to true.'
            );
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $container->setParameter('slcorp_role_model.default_role', $config['default_role']);
        $container->setParameter('slcorp_role_model.use_gid', $config['use_gid']);
        $container->setParameter('slcorp_role_model.secret_key', $config['secret_key']);
        $container->setParameter('slcorp_role_model.max_length', $config['max_length']);

        $this->filesystem = new Filesystem();
        $this->projectRoot = $container->getParameter('kernel.project_dir');

        if ($this->bundleLaunch) {
            $this->addDoctrineMappings($container);
            $this->addDoctrineMigrations($container);
            $this->addControllersRouting($container);
            $this->addDoctrineExtensionsConfig($container);
            $this->addApiPlatformConfig($container);
            $this->modifyDoctrineConfig($container);
            $this->ensureSecurityConfig($container);
        }
    }

    private function addDoctrineMappings(ContainerBuilder $container): void
    {
        $subDir = "/config/packages/doctrine";
        $filename = "role_model_bundle.php";
        $this->createConfigsFile($subDir, $filename);
    }

    private function addDoctrineMigrations(ContainerBuilder $container): void
    {
        $subDir = "/config/packages/doctrine_migrations";
        $filename = "1_role_model_bundle.php";
        $this->createConfigsFile($subDir, $filename);
    }

    private function addControllersRouting(ContainerBuilder $container): void
    {
        $subDir = "/config/packages/routes";
        $filename = "role_model_bundle.php";
        $this->createConfigsFile($subDir, $filename);
    }

    private function addDoctrineExtensionsConfig(ContainerBuilder $container): void
    {
        $subDir = "/config/packages";
        $filename = "doctrine_extensions.yaml";
        $this->createConfigsFile($subDir, $filename, remove: false);
    }

    private function createConfigsFile(string $subDir, string $filename, bool $remove = true): void
    {
        $projectConfigDir = $this->projectRoot . $subDir;
        $bundleMappingFile = __DIR__ . "/../..$subDir/$filename";
        $targetMappingFile = $projectConfigDir . "/$filename";

        if (!$this->filesystem->exists($projectConfigDir)) {
            $this->filesystem->mkdir($projectConfigDir, self::PERMISSIONS_MASK);
        }

        if (!$this->filesystem->exists($targetMappingFile)) {
            $this->filesystem->copy($bundleMappingFile, $targetMappingFile);
        } elseif ($remove) {
            $this->filesystem->remove($targetMappingFile);
            $this->filesystem->copy($bundleMappingFile, $targetMappingFile);
        }
    }

    private function addApiPlatformConfig(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('api_platform', [
            'mapping' => [
                'paths' => [
                    '%kernel.project_dir%/vendor/Slcorp/role-model-bundle/src/Domain/Entity',
                    '%kernel.project_dir%/vendor/Slcorp/role-model-bundle/src/Application/DTO',
                ],
            ],
        ]);
    }

    /**НЕ сработает если конфиг - PHP
     * @param ContainerBuilder $container
     * @return void
     */
    private function modifyDoctrineConfig(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'filters' => [
                    'soft_delete_filter' => [
                        'class' => 'Slcorp\RoleModelBundle\Infrastructure\Doctrine\Filter\SoftDeleteFilter',
                        'enabled' => true,
                    ],
                ],
            ],
        ]);
    }

    private function ensureSecurityConfig(ContainerBuilder $container): void
    {
        $projectConfigPath = $container->getParameter('kernel.project_dir') . '/config/packages/security.yaml';
        $bundleConfigPath = __DIR__ . '/../../config/packages/security.yaml';

        if (!file_exists($projectConfigPath)) {
            $this->filesystem->copy($bundleConfigPath, $projectConfigPath);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @return void
     * @throws Exception
     */
    public function prepend(ContainerBuilder $container)
    {
        if ($this->bundleLaunch) {
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config/packages'));
            $loader->load('gesdinet_jwt_refresh_token.yaml');
            $loader->load('lexik_jwt_authentication.yaml');
        }
    }
}
