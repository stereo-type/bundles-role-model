<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Infrastructure\Repository;

namespace Slcorp\RoleModelBundle\Infrastructure\Repository;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Slcorp\RoleModelBundle\Domain\Entity\Operation;
use Slcorp\RoleModelBundle\Domain\Repository\OperationRepositoryInterface;

/**
 * @template T of object
 * @template-extends NestedTreeRepository<T>
 */
class DoctrineOperationRepository extends NestedTreeRepository implements OperationRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($entityManager, $entityManager->getClassMetadata(Operation::class));
    }

    public function find(mixed $id, int|null|LockMode $lockMode = null, int|null $lockVersion = null): ?Operation
    {
        return $this->entityManager->getRepository(Operation::class)->find($id);
    }

    public function findByCode(string $code): ?Operation
    {
        return $this->entityManager->getRepository(Operation::class)->findOneBy(['code' => $code]);
    }

    public function findByRole(string $role, string|int $hydrationMode = AbstractQuery::HYDRATE_OBJECT): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('o')
            ->from(Operation::class, 'o')
            ->join('o.roles', 'r')
            ->where('r.name = :role')
            ->setParameter('role', $role)
            ->getQuery()
            ->getResult($hydrationMode);
    }

    public function findByRoles(array $roles, string|int $hydrationMode = AbstractQuery::HYDRATE_OBJECT): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('o')
            ->from(Operation::class, 'o')
            ->join('o.roles', 'r')
            ->where('r.name IN (:roles)')
            ->setParameter('roles', $roles)
            ->getQuery()
            ->getResult($hydrationMode);
    }


    public function findAll(): array
    {
        return $this->entityManager->getRepository(Operation::class)->findAll();
    }

    public function save(Operation $role, bool $flush = true): Operation
    {
        $this->entityManager->persist($role);
        if ($flush) {
            $this->entityManager->flush();
        }
        return $role;
    }

    public function delete(Operation $role, bool $flush = true): void
    {
        $this->entityManager->remove($role);
        if ($flush) {
            $this->entityManager->flush();
        }
    }


    public function buildChildrenTree(array $selectedItems = []): array
    {
        $qb = $this->getNodesHierarchyQueryBuilder();
        $qb->andWhere('node.delete IS NULL or node.delete = false');
        $data = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        return $this->buildTree($data);
    }
}
