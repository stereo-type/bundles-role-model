<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Domain\Repository;

use Slcorp\RoleModelBundle\Domain\Entity\Role;
use Slcorp\RoleModelBundle\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function find(int $id): ?User;

    public function findDeleted(int $id): ?User;

    public function findOneByLogin(string $username): ?User;

    public function findOneByADLogin(string $ad_login): ?User;

    public function save(User $user, bool $flush = true): User;

    public function findByEmail(string $email): ?User;

    public function delete(User $user, bool $flush = true): void;

    /**
     * @return User[]
     */
    public function findByRole(Role $role): array;
}
