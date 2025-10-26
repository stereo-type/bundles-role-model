<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\UseCase\Role;

use Slcorp\RoleModelBundle\Application\Service\Role\RoleService;

readonly class RoleDelete
{
    public function __construct(private RoleService $service)
    {
    }

    public function execute(int $id): bool
    {
        return $this->service->deleteRole($id);
    }
}
