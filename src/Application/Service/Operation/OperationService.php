<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Service\Operation;

use Slcorp\RoleModelBundle\Application\DTO\OperationCreateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\Mapper\OperationMapper;
use Slcorp\RoleModelBundle\Application\Service\DTOMerger;
use Slcorp\RoleModelBundle\Domain\Entity\Operation;
use Slcorp\RoleModelBundle\Domain\Entity\Role;
use Slcorp\RoleModelBundle\Domain\Repository\OperationRepositoryInterface;
use Slcorp\RoleModelBundle\Domain\Repository\RoleRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class OperationService
{
    public function __construct(
        private OperationRepositoryInterface $repository,
        private RoleRepositoryInterface $roleRepository,
        private OperationMapper $mapper,
        private DTOMerger $DTOMerger,
    ) {
    }

    public function create(OperationCreateDTO $dto, bool $flush = true): Operation
    {
        if ($this->repository->findByCode($dto->getCode())) {
            throw BundleException::operationAlreadyExistCode($dto->getCode());
        }

        return $this->repository->save($this->mapper->fromDto($dto, $this->repository), $flush);
    }

    public function delete(int $id, bool $flush = true): bool
    {
        if (!$role = $this->repository->find($id)) {
            throw BundleException::operationNotFound($id);
        }
        $this->repository->delete($role, $flush);

        return true;
    }

    public function find(int $id): ?Operation
    {
        return $this->repository->find($id);
    }

    public function findByCode(string $code): ?Operation
    {
        return $this->repository->findByCode($code);
    }

    public function findByRole(string $role): array
    {
        return $this->repository->findByRole($role);
    }


    public function getAllOperations(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @param object|null $node
     * @param bool $direct
     * @param array<string, mixed> $options
     * @param bool $includeNode
     * @return array<int, array<string, mixed>>|string
     * @see \Gedmo\Tree\RepositoryUtilsInterface::childrenHierarchy
     */
    public function childrenHierarchy($node = null, $direct = false, array $options = [], $includeNode = false)
    {
        return $this->repository->childrenHierarchy($node, $direct, $options, $includeNode);
    }

    /**
     * @param array<int|string, object> $nodes
     * @param array<string, mixed> $options
     * @return array<int, array<string, mixed>>|string
     * @see \Gedmo\Tree\RepositoryUtilsInterface::buildTree
     */
    public function buildTree(array $nodes, array $options = [])
    {
        return $this->repository->buildTree($nodes, $options);
    }

    /**
     * @param object[] $nodes
     * @return array<int, array<string, mixed>>
     * @see \Gedmo\Tree\RepositoryUtilsInterface::buildTreeArray
     */
    public function buildTreeArray(array $nodes)
    {
        return $this->repository->buildTreeArray($nodes);
    }

    /**
     * @param string $childrenIndex
     * @return void
     * @see \Gedmo\Tree\RepositoryUtilsInterface::setChildrenIndex
     */
    public function setChildrenIndex(string $childrenIndex)
    {
        $this->repository->setChildrenIndex($childrenIndex);
    }

    /**
     * @return string
     * @see \Gedmo\Tree\RepositoryUtilsInterface::getChildrenIndex
     */
    public function getChildrenIndex()
    {
        return $this->repository->getChildrenIndex();
    }


    /** Возвращает дерево в формате JsTree
     * @param array $selectedItems
     * @return array
     */
    public function buildChildrenTree(array $selectedItems = []): array
    {
        $capabilities = $this->repository->buildChildrenTree();

        return $this->processHierarchy($capabilities, $selectedItems);
    }


    public function processHierarchy(array $capabilities, array $selectedCapabilities): array
    {
        foreach ($capabilities as &$capability) {
            $capability['text'] = '<span>' . $capability['name'] . '</span>';
            $capability['state'] = [
                'selected' => in_array($capability['id'], $selectedCapabilities, true),
                'opened' => true,
            ];
            if (isset($capability['__children'])) {
                $capability['children'] = $this->processHierarchy($capability['__children'], $selectedCapabilities);
                unset($capability['__children']);
            }
        }

        return $capabilities;
    }

    /**
     * @param string $name
     * @return Operation[]
     */
    public function getOperationsByRole(string $name): array
    {
        return $this->repository->findByRole($name);
    }

    public function getOperationById(int $id): ?Operation
    {
        return $this->repository->find($id);
    }

    public function update(OperationCreateDTO $dto, Operation $targetOperation, bool $flush = true): Operation
    {
        $operationEntity = $this->mapper->fromDto($dto, $this->repository);
        $this->DTOMerger->mergeDtoIntoEntity($targetOperation, $operationEntity);

        return $this->repository->save($targetOperation, $flush);
    }

    public function addOperationToRole(Operation $operation, Role $role, bool $flush = true): Role
    {
        $role->addOperation($operation);

        return $this->roleRepository->save($role, $flush);
    }

    public function addOperationToRoleId(string $operationCode, int $roleId, bool $flush = true): Role
    {
        [$operation, $role] = $this->getOperationAndRole($operationCode, $roleId);

        return $this->addOperationToRole($operation, $role, $flush);
    }

    public function removeOperationFromRole(Operation $operation, Role $role, bool $flush = true): Role
    {
        $role->removeOperation($operation);

        return $this->roleRepository->save($role, $flush);
    }

    public function removeOperationFromRoleId(string $operationCode, int $roleId, bool $flush = true): Role
    {
        [$operation, $role] = $this->getOperationAndRole($operationCode, $roleId);

        return $this->removeOperationFromRole($operation, $role, $flush);
    }

    /**
     * @param UserInterface $user
     * @param bool $only_codes
     * @return Operation[]|string[]
     */
    public function flatOperationsList(UserInterface $user, bool $only_codes = true): array
    {
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            $operations = $this->repository->findAll();
        } else {
            $operations = $this->repository->findByRoles($roles);
        }

        $result = [];
        foreach ($operations as $operation) {
            if (!array_key_exists($operation->getId(), $result)) {
                $result[$operation->getId()] = $operation;
            }
            $children = $this->repository->getChildren($operation);
            foreach ($children as $child) {
                if (!array_key_exists($child->getId(), $result)) {
                    $result[$child->getId()] = $child;
                }
            }
        }

        if ($only_codes) {
            $result = array_map(static fn (Operation $operation) => $operation->getCode(), $result);
        }

        return $result;
    }

    private function getOperationAndRole(string $operationCode, int $roleId): array
    {
        $operation = $this->repository->findByCode($operationCode);
        if (!$operation) {
            throw BundleException::operationNotFoundCode($operationCode);
        }

        $role = $this->roleRepository->find($roleId);
        if (!$role) {
            throw BundleException::roleNotFound($roleId);
        }

        return [$operation, $role];
    }

}
