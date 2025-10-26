<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Domain\Repository;

use Slcorp\RoleModelBundle\Domain\Entity\Email;
use Slcorp\RoleModelBundle\Domain\Entity\User;

interface EmailRepositoryInterface
{
    public function find(int $id): ?Email;

    public function save(Email $email, bool $flush = true): Email;

    public function findByEmail(string $email): ?Email;

    public function delete(Email $role, bool $flush = true): void;

    public function findAllByUser(User $user): array;
}
