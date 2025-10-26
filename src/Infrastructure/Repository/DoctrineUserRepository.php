<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Infrastructure\Repository;

use Slcorp\RoleModelBundle\Domain\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Slcorp\RoleModelBundle\Domain\Entity\User;
use Slcorp\RoleModelBundle\Domain\Repository\UserRepositoryInterface;

readonly class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function find(int $id): ?User
    {
        return $this->entityManager->getRepository(User::class)->find($id);
    }


    public function findDeleted(int $id): ?User
    {
        $this->entityManager->getFilters()->disable('soft_delete_filter');
        $user = $this->entityManager->getRepository(User::class)->find($id);
        $this->entityManager->getFilters()->enable('soft_delete_filter');
        if (!$user?->isDelete()) {
            return null;
        }

        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('DISTINCT u')
            ->from(User::class, 'u')
            ->leftJoin('u.emails', 'e')
            ->where('e.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(User $user, bool $flush = true): User
    {
        $this->entityManager->persist($user);
        if ($flush) {
            $this->entityManager->flush();
        }

        return $user;
    }

    public function delete(User $user, bool $flush = true): void
    {
        $this->entityManager->remove($user);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function findOneByLogin(string $username): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
    }

    public function findOneByADLogin(string $ad_login): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['ad_login' => $ad_login]);
    }


    public function findByRole(Role $role): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->join('u.userRoles', 'r')
            ->where('r = :role')
            ->setParameter('role', $role)
            ->getQuery()
            ->getResult();
    }
}
