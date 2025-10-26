<?php

declare(strict_types=1);

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

if (!function_exists('base_rouses')) {
    function base_rouses(): array
    {
        $routes = [
//            [
//                'path' => '../src/Controller/',
//                'namespace' => 'App\Controller',
//            ]
        ];
        return $routes;
    }
}

if (!function_exists('add_controllers')) {
    function add_controllers(array &$routes, string $path): void
    {
        $root = dirname(__DIR__);
        $baseDir = $root . '/src/' . $path;

        if (is_dir($baseDir)) {
            try {
                $controllerBaseDir = 'Controller';
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );
                foreach ($iterator as $file) {
                    /**@var $file SplFileInfo */
                    if ($file->isDir() && $file->getFilename() === $controllerBaseDir) {
                        $relativePath = str_replace($baseDir . '/', '', $file->getPath());
                        $namespace = 'App\\Features\\' . str_replace('/', '\\', $relativePath) . '\\' . $controllerBaseDir;
                        $route = [
                            'path' => "../src/Features/$relativePath/$controllerBaseDir",
                            'namespace' => $namespace,
                        ];
                        $routes[] = $route;
                    }
                }
            } catch (Throwable $e) {
                error_log($e->getMessage());
            }
        } else {
            error_log("Путь для подключения контроллеров '$baseDir' не существует");
        }
    }
}

if (!function_exists('controllers_paths')) {
    function controllers_paths(): array
    {
        $filesystem = new Filesystem();
        $dir = __DIR__ . '/packages/routes/';
        $routes = base_rouses();
        if ($filesystem->exists($dir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                /**@var $file SplFileInfo */
                if (!$file->isDir() && str_ends_with($file->getFilename(), '.php')) {
                    $controllersFromFile = include $file->getPathname();
                    if (is_array($controllersFromFile)) {
                        foreach ($controllersFromFile as $namespace => $path) {
                            $routes[] = [
                                'path' => str_replace('%kernel.project_dir%', '../', $path),
                                'namespace' => $namespace,
                            ];
                        }
                    }
                }
            }
        }

        add_controllers($routes, 'Features');
        add_controllers($routes, 'Bundles');

        return $routes;
    }
}


return static function (RoutingConfigurator $routingConfigurator): void {
    $controllers = controllers_paths();
    foreach ($controllers as $controller) {
        $routingConfigurator->import($controller, 'attribute');
    }
};
