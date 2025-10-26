<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Service\Role;

use Slcorp\RoleModelBundle\Application\DTO\RoleCreateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\Mapper\RoleMapper;
use Slcorp\RoleModelBundle\Application\Service\DTOMerger;
use Slcorp\RoleModelBundle\Domain\Entity\Role;
use Slcorp\RoleModelBundle\Domain\Entity\User;
use Slcorp\RoleModelBundle\Domain\Repository\RoleRepositoryInterface;
use Slcorp\RoleModelBundle\Domain\Repository\UserRepositoryInterface;

readonly class RoleService
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private UserRepositoryInterface $userRepository,
        private RoleMapper $mapper,
        private DTOMerger $DTOMerger,
    ) {
    }

    public function create(RoleCreateDTO $dto, bool $flush = true): Role
    {
        $this->fixName($dto);
        if ($this->roleRepository->findByName($dto->getName())) {
            throw BundleException::roleAlreadyExistName($dto->getName());
        }
        return $this->roleRepository->save($this->mapper->fromDto($dto), $flush);
    }

    public function deleteRole(int $id): bool
    {
        if (!$role = $this->roleRepository->find($id)) {
            throw BundleException::roleNotFound($id);
        }
        $this->roleRepository->delete($role);
        return true;
    }


    public function assignRoleToUserName(int $userId, string $roleName, bool $flush = true): User
    {
        /**@var User $user */
        /**@var Role $role */
        [$user, $role] = $this->getUserAndRoleName($userId, $roleName);
        $roles = $user->getUserRoles()->map(fn (Role $role) => $role->getName())->toArray();
        if (in_array($role->getName(), $roles, true)) {
            return $user;
        }
        $user->addRole($role);
        return $this->userRepository->save($user, $flush);
    }

    public function assignRoleToUser(int $userId, int $roleId, bool $flush = true): User
    {
        /**@var User $user */
        /**@var Role $role */
        [$user, $role] = $this->getUserAndRole($userId, $roleId);
        $roles = $user->getUserRoles()->map(fn (Role $role) => $role->getName())->toArray();
        if (in_array($role->getName(), $roles, true)) {
            return $user;
        }
        $user->addRole($role);
        return $this->userRepository->save($user, $flush);
    }


    public function removeRoleFromUserName(int $userId, string $roleName, bool $flush = true): User
    {
        /**@var User $user */
        /**@var Role $role */
        [$user, $role] = $this->getUserAndRoleName($userId, $roleName);
        $user->removeRole($role);
        return $this->userRepository->save($user, $flush);
    }

    public function removeRoleFromUser(int $userId, int $roleId, bool $flush = true): User
    {
        /**@var User $user */
        /**@var Role $role */
        [$user, $role] = $this->getUserAndRole($userId, $roleId);
        $user->removeRole($role);
        return $this->userRepository->save($user, $flush);
    }

    public function getAllRoles(): array
    {
        return $this->roleRepository->findAll();
    }

    public function getRoleByName(string $name): ?Role
    {
        return $this->roleRepository->findByName($name);
    }

    public function getRoleById(int $id): ?Role
    {
        return $this->roleRepository->find($id);
    }

    private function getUserAndRole(int $userId, int $roleId): array
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            throw BundleException::userNotFound($userId);
        }

        $role = $this->roleRepository->find($roleId);
        if (!$role) {
            throw BundleException::roleNotFound($roleId);
        }

        return [$user, $role];
    }

    private function getUserAndRoleName(int $userId, string $roleName): array
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            throw BundleException::userNotFound($userId);
        }

        $role = $this->roleRepository->findByName($roleName);
        if (!$role) {
            throw BundleException::roleNotFoundName($roleName);
        }

        return [$user, $role];
    }

    public function update(RoleCreateDTO $dto, Role $targetRole, bool $flush): Role
    {
        $this->fixName($dto);
        $roleEntity = $this->mapper->fromDto($dto);
        $this->DTOMerger->mergeDtoIntoEntity($targetRole, $roleEntity);
        return $this->roleRepository->save($targetRole, $flush);
    }

    private function fixName(RoleCreateDTO $dto): void
    {
        $name = $dto->getName();
        if (!str_starts_with($name, 'ROLE_')) {
            $name = 'ROLE_' . $name;
            $dto->setName($name);
        }
    }
}
