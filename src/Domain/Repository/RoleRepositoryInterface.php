<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Domain\Repository;

use Slcorp\RoleModelBundle\Domain\Entity\Role;

interface RoleRepositoryInterface
{
    public function find(int $id): ?Role;

    public function save(Role $role, bool $flush = true): Role;

    public function findByName(string $name): ?Role;

    public function delete(Role $role, bool $flush = true): void;

    public function findAll(): array;
}
