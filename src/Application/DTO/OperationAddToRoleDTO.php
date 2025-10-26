<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\DTO;

use Slcorp\CoreBundle\Application\DTO\DTOTrait;
use Slcorp\RoleModelBundle\Domain\Entity\Operation;
use Slcorp\RoleModelBundle\Domain\Entity\Role;
use Slcorp\CoreBundle\Infrastructure\Validator\Constraints as CustomAssert;

class OperationAddToRoleDTO
{
    use DTOTrait;

    #[CustomAssert\NotBlank]
    #[CustomAssert\MustExist(entityClass: Operation::class, field: 'code', message: 'Операция "{{ value }}" не найдена')]
    public string $operationCode;

    #[CustomAssert\NotBlank]
    #[CustomAssert\MustExist(entityClass: Role::class, message: 'Роль "{{ value }}" не найдена')]
    public int $roleId;

    public function getOperationCode(): string
    {
        return $this->operationCode;
    }

    public function setOperationCode(string $operationCode): OperationAddToRoleDTO
    {
        $this->operationCode = $operationCode;
        return $this;
    }

    public function getRoleId(): int
    {
        return $this->roleId;
    }

    public function setRoleId(int $roleId): OperationAddToRoleDTO
    {
        $this->roleId = $roleId;
        return $this;
    }



}
