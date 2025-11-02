<?php

/**
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Enum;

enum CapabilityAction: string
{
    case CREATE = 'create';
    case CREATE_SELF = 'create_self';
    case EDIT = 'edit';
    case EDIT_SELF = 'edit_self';
    case DELETE = 'delete';
    case DELETE_SELF = 'delete_self';
    case VIEW = 'view';
    case VIEW_SELF = 'view_self';
    case LIST = 'list';

    public function name(): string
    {
        return match ($this) {
            self::CREATE => 'Создание',
            self::EDIT => 'Редактирование',
            self::DELETE => 'Удаление',
            self::VIEW => 'Просмотр',
            self::LIST => 'Просмотр списка',
            self::VIEW_SELF => 'Просмотр своего',
            self::CREATE_SELF => 'Создание свое',
            self::EDIT_SELF => 'Редактирование свое',
            self::DELETE_SELF => 'Удаление свое',
        };
    }
}
