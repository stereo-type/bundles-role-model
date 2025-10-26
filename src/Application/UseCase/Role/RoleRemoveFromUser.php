<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\UseCase\Role;

use Slcorp\RoleModelBundle\Application\DTO\RoleRemoveFromUserDTO;
use Slcorp\RoleModelBundle\Application\DTO\RoleRemoveFromUserDTOName;
use Slcorp\RoleModelBundle\Application\Service\Role\RoleService;
use Slcorp\RoleModelBundle\Domain\Entity\User;

readonly class RoleRemoveFromUser
{
    public function __construct(private RoleService $service)
    {
    }


    public function execute(RoleRemoveFromUserDTO $dto): User
    {
        return $this->service->removeRoleFromUser($dto->userId, $dto->roleId);
    }
    public function executeFromName(RoleRemoveFromUserDTOName $dto): User
    {
        return $this->service->removeRoleFromUserName($dto->userId, $dto->roleName);
    }
}
