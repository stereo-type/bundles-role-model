<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\UseCase\User;

use Slcorp\RoleModelBundle\Application\Service\User\UserService;

readonly class UserRestore
{
    public function __construct(private UserService $service)
    {
    }

    public function execute(int $id, bool $flush = true): bool
    {
        return $this->service->restore($id, $flush);
    }

}
