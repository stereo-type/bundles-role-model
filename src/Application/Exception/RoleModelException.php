<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Exception;

class RoleModelException extends \Exception
{
    /**@deprecated
     * @param int $userId
     * @return self
     */
    public static function userNotFound(int $userId): self
    {
        return new self(sprintf('User with ID %d not found.', $userId));
    }

    /**@deprecated */
    public static function roleNotFound(string $roleName): self
    {
        return new self(sprintf('Role with name "%s" not found.', $roleName));
    }

    /**@deprecated */
    public static function roleAlreadyExist(string $roleName): self
    {
        return new self(sprintf('Role with name "%s" already exist.', $roleName));
    }
}
