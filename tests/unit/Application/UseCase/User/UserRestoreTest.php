<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\User;

use Slcorp\RoleModelBundle\Application\Service\User\UserService;
use Slcorp\RoleModelBundle\Application\UseCase\User\UserDelete;
use Slcorp\RoleModelBundle\Application\UseCase\User\UserRestore;

class UserRestoreTest extends UserTest
{
    private UserDelete $useCaseDelete;
    private UserRestore $useCaseDBRestore;
    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCaseDelete = $this->container->get(UserDelete::class);
        $this->useCaseDBRestore = $this->container->get(UserRestore::class);
        $this->userService = $this->container->get(UserService::class);
    }


    public function testRestoreSuccessful(): void
    {
        $user = clone($this->createTempUserInDb());
        $userId = $user->getId();
        $this->assertNotEquals($this->userService->getUserById($userId), null);
        $this->useCaseDelete->execute($userId);
        $this->assertEquals($this->userService->getUserById($userId)->isDelete(), true);
        $this->useCaseDBRestore->execute($userId); //
        $this->assertEquals($this->userService->getUserById($userId)->isDelete(), false);
    }


}
