<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\UseCase\User;

use Doctrine\ORM\EntityManagerInterface;
use Slcorp\RoleModelBundle\Application\Service\User\UserService;

readonly class UserDelete
{
    public function __construct(
        private UserService $service,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function execute(int $id, bool $flush = true): bool
    {
        return $this->service->delete($id, $flush);
    }

    public function deleteUsers(array $ids, bool $flush = true): void
    {
        foreach ($ids as $id) {
            $this->service->delete($id, false);
        }
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
