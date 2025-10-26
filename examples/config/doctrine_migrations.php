<?php

/**
 * @package    doctrine_migrations.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Config\DoctrineMigrationsConfig;


if (!function_exists('migrations_paths')) {
    function migrations_paths(): array
    {
        $filesystem = new Filesystem();
        $migrations = [
            'DoctrineMigrations' => '%kernel.project_dir%/migrations',
        ];

        $dir = __DIR__ . '/doctrine_migrations/';
        if ($filesystem->exists($dir)) {
            $iterator = new  RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                /**@var $file SplFileInfo */
                if (!$file->isDir() && str_ends_with($file->getFilename(), '.php')) {
                    $migrationsFromFile = include $file->getPathname();
                    if (is_array($migrationsFromFile)) {
                        foreach ($migrationsFromFile as $key => $value) {
                            $migrations[$key] = $value;
                        }
                    }
                }
            }
        }

        return $migrations;
    }
}

return static function (DoctrineMigrationsConfig $config): void {
    $paths = migrations_paths();
    foreach ($paths as $namespace => $path) {
        $config->migrationsPath($namespace, $path);
    }
    $config->enableProfiler(false);
};