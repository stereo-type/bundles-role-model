<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Domain\Repository;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\AbstractQuery;
use Slcorp\RoleModelBundle\Domain\Entity\Operation;

interface OperationRepositoryInterface
{
    public function save(Operation $role, bool $flush = true): Operation;

    public function findByCode(string $code): ?Operation;

    public function find(mixed $id, int|null|LockMode $lockMode = null, int|null $lockVersion = null): ?Operation;

    /**
     * @param string $role
     * @param string|int $hydrationMode
     * @return Operation[]
     */
    public function findByRole(string $role, string|int $hydrationMode = AbstractQuery::HYDRATE_OBJECT): array;

    /**
     * @param array $roles
     * @param string|int $hydrationMode
     * @return Operation[]
     */
    public function findByRoles(array $roles, string|int $hydrationMode = AbstractQuery::HYDRATE_OBJECT): array;

    public function delete(Operation $role, bool $flush = true): void;

    /**
     * @return Operation[]
     */
    public function findAll(): array;

    /**
     * @param object|null $node
     * @param bool $direct
     * @param array<string, mixed> $options
     * @param bool $includeNode
     * @return array<int, array<string, mixed>>|string
     * @see \Gedmo\Tree\RepositoryUtilsInterface::childrenHierarchy
     */
    public function childrenHierarchy($node = null, $direct = false, array $options = [], $includeNode = false);

    /**
     * @param array<int|string, object> $nodes
     * @param array<string, mixed> $options
     * @return array<int, array<string, mixed>>|string
     * @see \Gedmo\Tree\RepositoryUtilsInterface::buildTree
     */
    public function buildTree(array $nodes, array $options = []);

    /**
     * @param object[] $nodes
     * @return array<int, array<string, mixed>>
     * @see \Gedmo\Tree\RepositoryUtilsInterface::buildTreeArray
     */
    public function buildTreeArray(array $nodes);

    /**
     * @param string $childrenIndex
     * @return void
     * @see \Gedmo\Tree\RepositoryUtilsInterface::setChildrenIndex
     */
    public function setChildrenIndex($childrenIndex);

    /**
     * @return string
     * @see \Gedmo\Tree\RepositoryUtilsInterface::getChildrenIndex
     */
    public function getChildrenIndex();


    /**
     * @param object|null $node
     * @param bool $direct
     * @param string|string[]|null $sortByField
     * @param string|string[] $direction
     * @param bool $includeNode
     * @return array<int, object>
     * @see \Gedmo\Tree\Entity\Repository\NestedTreeRepository::getChildren
     */
    public function getChildren($node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false);


    /**Метод для построения дерева для плагина типа JsTree
     * @param array $selectedItems
     * @return array
     */
    public function buildChildrenTree(array $selectedItems = []): array;
}
