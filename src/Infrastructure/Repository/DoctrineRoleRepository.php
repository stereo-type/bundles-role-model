<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Infrastructure\Repository;

namespace Slcorp\RoleModelBundle\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Slcorp\RoleModelBundle\Domain\Entity\Role;
use Slcorp\RoleModelBundle\Domain\Repository\RoleRepositoryInterface;

readonly class DoctrineRoleRepository implements RoleRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function find(int $id): ?Role
    {
        return $this->entityManager->getRepository(Role::class)->find($id);
    }

    public function findByName(string $name): ?Role
    {
        return $this->entityManager->getRepository(Role::class)->findOneBy(['name' => $name]);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Role::class)->findAll();
    }

    public function save(Role $role, bool $flush = true): Role
    {
        $this->entityManager->persist($role);
        if ($flush) {
            $this->entityManager->flush();
        }
        return $role;
    }

    public function delete(Role $role, bool $flush = true): void
    {
        $this->entityManager->remove($role);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
