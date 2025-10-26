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
    case EDIT = 'edit';
    case DELETE = 'delete';
    case VIEW = 'view';
    case LIST = 'list';

    public function name(): string
    {
        return match ($this) {
            self::CREATE => 'Создание',
            self::EDIT => 'Редактирование',
            self::DELETE => 'Удаление',
            self::VIEW => 'Просмотр',
            self::LIST => 'Просмотр списка',
        };
    }
}
