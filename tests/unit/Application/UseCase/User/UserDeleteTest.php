<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\User;

use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\UseCase\User\UserDelete;
use Slcorp\RoleModelBundle\Domain\Entity\User;

class UserDeleteTest extends UserTest
{
    private UserDelete $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new UserDelete($this->service, $this->mockEntityManager);
    }

    public function testHandleDeleteUserException(): void
    {
        $testId = -1;
        $this->expectExceptionObject(BundleException::userNotFound($testId));
        $this->useCase->execute($testId);
    }

    public function testHandleDeleteUser(): void
    {
        $testId = -1;

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($testId)
            ->willReturnCallback(function ($testId) {
                $user  = new User();
                $user->setId($testId);
                $user->setUsername('test_user');
                return $user;
            });

        $result = $this->useCase->execute($testId);
        $this->assertTrue($result);
    }


}
