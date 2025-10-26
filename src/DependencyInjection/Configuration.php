<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('academ_city_role_model');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('default_role')
                   ->defaultValue('ROLE_USER')
                ->end()
                ->booleanNode('use_gid')
                    ->defaultFalse() // По умолчанию GID не используется
                ->end()
                ->scalarNode('secret_key')
                    ->defaultNull() // Секретный ключ не обязателен, если GID не нужен
                     ->info('Секретный ключ для формирования GID. Обязателен, если use_gid = true.')
                ->end()
                ->integerNode('max_length')
                    ->defaultValue(512)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
