<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Infrastructure\Repository;

namespace Slcorp\RoleModelBundle\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Slcorp\RoleModelBundle\Domain\Entity\Email;
use Slcorp\RoleModelBundle\Domain\Entity\User;
use Slcorp\RoleModelBundle\Domain\Repository\EmailRepositoryInterface;

readonly class DoctrineEmailRepository implements EmailRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function find(int $id): ?Email
    {
        return $this->entityManager->getRepository(Email::class)->find($id);
    }

    public function findByEmail(string $email): ?Email
    {
        return $this->entityManager->getRepository(Email::class)->findOneBy(['email' => $email]);
    }

    public function findAllByUser(User $user): array
    {
        return $this->entityManager->getRepository(Email::class)->findBy(['user' => $user->getId()]);
    }

    public function save(Email $email, bool $flush = true): Email
    {
        $this->entityManager->persist($email);
        if ($flush) {
            $this->entityManager->flush();
        }
        return $email;
    }

    public function delete(Email $role, bool $flush = true): void
    {
        $this->entityManager->remove($role);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
