<?php
/**
 * @package    role_model_bundle.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

return [
    'Slcorp\RoleModelBundle' => [
        'type'      => 'attribute',
        'is_bundle' => false,
        'dir'       => '%kernel.project_dir%/vendor/slcorp/role-model-bundle/src/Domain/Entity',
        'prefix'    => 'Slcorp\RoleModelBundle\Domain\Entity',
        'alias'     => 'Slcorp\RoleModelBundle',
    ],
];