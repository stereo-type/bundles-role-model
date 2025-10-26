<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\Role;

use Slcorp\RoleModelBundle\Application\DTO\RoleRemoveFromUserDTOName;
use Slcorp\RoleModelBundle\Application\UseCase\Role\RoleRemoveFromUser;
use Slcorp\RoleModelBundle\Domain\Entity\Role;
use Slcorp\RoleModelBundle\Domain\Entity\User;

class RoleRemoveTest extends RoleTest
{
    private RoleRemoveFromUser $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new RoleRemoveFromUser($this->service);
    }

    public function testAssignRoleTOUserSuccessMock(): void
    {
        $testUserId = -1;
        $testRoleId = -2;
        $testRoleName = 'ROLE';

        $testUser = new User();
        $testUser->setId($testUserId);


        $testRole = new Role();
        $testRole->setId($testRoleId);
        $testRole->setName($testRoleName);

        $testUser->addRole($testRole);

        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->with($testUserId)
            ->willReturnCallback(function ($testUserId) use ($testUser) {
                return clone $testUser;
            });

        $this->repository
            ->expects($this->once())
            ->method('findByName')
            ->with($testRoleName)
            ->willReturnCallback(function ($testRoleName) use ($testRole) {
                return clone $testRole;
            });

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function ($userInstance) use ($testUser) {
                return clone $testUser;
            });


        $arrayData = [
            'userId' => $testUserId,
            'roleName' => $testRoleName,
        ];
        $dto = RoleRemoveFromUserDTOName::fromArray($arrayData);
        $user = $this->useCase->executeFromName($dto);
        $roles = $user->getRoles();
        $this->assertEquals('ROLE_USER', end($roles));
        $this->assertSame(count($roles), 1);
    }


}
