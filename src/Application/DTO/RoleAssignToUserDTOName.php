<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\DTO;

use Slcorp\CoreBundle\Application\DTO\DTOTrait;
use Slcorp\RoleModelBundle\Domain\Entity\Role;
use Slcorp\RoleModelBundle\Domain\Entity\User;
use Slcorp\CoreBundle\Infrastructure\Validator\Constraints as CustomAssert;

class RoleAssignToUserDTOName
{
    use DTOTrait;
    #[CustomAssert\NotBlank]
    #[CustomAssert\MustExist(entityClass: User::class, message: 'Пользователь "{{ value }}" не найден')]
    public int $userId;

    #[CustomAssert\NotBlank]
    #[CustomAssert\MustExist(entityClass: Role::class, field: 'name', message: 'Роль "{{ value }}" не найдена')]
    public string $roleName;

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): RoleAssignToUserDTOName
    {
        $this->userId = $userId;
        return $this;
    }

    public function getRoleName(): string
    {
        return $this->roleName;
    }

    public function setRoleName(string $roleName): RoleAssignToUserDTOName
    {
        $this->roleName = $roleName;
        return $this;
    }




}
