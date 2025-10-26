<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Filesystem\Filesystem;

if (!function_exists('base_mapping')) {
    function base_mapping(): array
    {
        $mappings = [
//            'App' => [
//                'type'      => 'attribute',
//                'is_bundle' => false,
//                'dir'       => '%kernel.project_dir%/src/Entity',
//                'prefix'    => 'App\Entity',
//                'alias'     => 'App',
//            ],
        ];
        return $mappings;
    }
}

if (!function_exists('add_mappings')) {
    function add_mappings(array &$mappings, string $path): void
    {
        $root = dirname(__DIR__, 2);
        $baseDir = $root . '/src/' . $path;

        if (is_dir($baseDir) && (new FilesystemIterator($baseDir))->valid()) {
            try {
                $entityBaseDir = 'Entity';
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );
                foreach ($iterator as $file) {
                    /**@var $file SplFileInfo */
                    if ($file->isDir() && $file->getFilename() === $entityBaseDir) {
                        if ((new FilesystemIterator($file->getPathname()))->valid()) {
                            $relativePath = str_replace($baseDir . '/', '', $file->getPath());
                            $namespace = str_replace('/', '\\', $relativePath);

                            $mappings[$namespace] = [
                                'type' => 'attribute',
                                'dir' => "%kernel.project_dir%/src/$path/$relativePath/$entityBaseDir",
                                'is_bundle' => false,
                                'prefix' => "App\\$path\\$namespace\\$entityBaseDir",
                                'alias' => str_replace('/', '_', $relativePath),
                            ];
                        } else {
                            error_log("Папка для маппинга сущностей '$baseDir' пуста");
                        }
                    }
                }
            } catch (Throwable $e) {
                error_log($e->getMessage());
            }
        } else {
            error_log("Путь для маппинга сущностей '$baseDir' не существует");
        }
    }
}

if (!function_exists('doctrine_mappings')) {
    function doctrine_mappings(): array
    {
        $filesystem = new Filesystem();
        $mappings = base_mapping();

        $dir = __DIR__ . '/doctrine/';
        if ($filesystem->exists($dir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($iterator as $file) {
                /**@var $file SplFileInfo */
                if (!$file->isDir() && str_ends_with($file->getFilename(), '.php')) {
                    $mappingsFromFile = include $file->getPathname();
                    if (is_array($mappingsFromFile)) {
                        foreach ($mappingsFromFile as $key => $value) {
                            $mappings[$key] = $value;
                        }
                    }
                }
            }
        }

        add_mappings($mappings, 'Features');
        add_mappings($mappings, 'Bundles');
        add_mappings($mappings, 'Domain');

        return $mappings;
    }
}
return static function (ContainerConfigurator $containerConfigurator): void {
    $mapping = doctrine_mappings();
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'url' => '%env(resolve:DATABASE_URL)%',
        ],
        'orm' => [
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
            'auto_mapping' => true,
            'mappings' => $mapping,
            'filters' => [
                'soft_delete_filter' => [
                    'class' => 'Slcorp\RoleModelBundle\Infrastructure\Doctrine\Filter\SoftDeleteFilter',
                    'enabled' => true,
                ],
            ]
        ],
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('doctrine', [
            'dbal' => [
                'dbname_suffix' => '_test%env(default::TEST_TOKEN)%',
            ],
        ]);
    }
    if ($containerConfigurator->env() === 'prod') {
        $containerConfigurator->extension('doctrine', [
            'orm' => [
                'proxy_dir' => '%kernel.build_dir%/doctrine/orm/Proxies',
                'query_cache_driver' => [
                    'type' => 'pool',
                    'pool' => 'doctrine.system_cache_pool',
                ],
                'result_cache_driver' => [
                    'type' => 'pool',
                    'pool' => 'doctrine.result_cache_pool',
                ],
            ],
        ]);
        $containerConfigurator->extension('framework', [
            'cache' => [
                'pools' => [
                    'doctrine.result_cache_pool' => [
                        'adapter' => 'cache.app',
                    ],
                    'doctrine.system_cache_pool' => [
                        'adapter' => 'cache.system',
                    ],
                ],
            ],
        ]);
    }
};
