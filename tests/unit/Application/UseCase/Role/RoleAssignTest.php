<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\Role;

use Slcorp\RoleModelBundle\Application\DTO\RoleAssignToUserDTO;
use Slcorp\RoleModelBundle\Application\DTO\RoleAssignToUserDTOName;
use Slcorp\RoleModelBundle\Application\UseCase\Role\RoleAssignToUser;
use Slcorp\RoleModelBundle\Domain\Entity\Role;
use Slcorp\RoleModelBundle\Domain\Entity\User;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\RoleAssignRequestHandler;
use Symfony\Component\HttpFoundation\Request;

class RoleAssignTest extends RoleTest
{
    private RoleAssignToUser $useCase;
    private RoleAssignRequestHandler $handlerAssign;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new RoleAssignToUser($this->service, $this->validationService);
        $this->handlerAssign = new RoleAssignRequestHandler($this->serializer);
        $_ENV['DISABLE_ASSERTS'] = true;
    }

    public function testAssignRoleTOUserSuccessMockFromName(): void
    {
        $testUserId = -1;
        $testRoleName = 'ROLE';

        $testUser = new User();
        $testRole = new Role();
        $testRole->setName($testRoleName);

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
        $dto = RoleAssignToUserDTOName::fromArray($arrayData);


        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handlerAssign->handleForName($request);

        $user = $this->useCase->executeFromName($result);

        $roles = $user->getRoles();
        $this->assertEquals($testRoleName, reset($roles));
    }
    public function testAssignRoleTOUserSuccessMockFromId(): void
    {
        $testUserId = -1;
        $testRoleId = -1;
        $testRoleName = 'ROLE';

        $testUser = new User();
        $testRole = new Role();
        $testRole->setName($testRoleName);

        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->with($testUserId)
            ->willReturnCallback(function ($testUserId) use ($testUser) {
                return clone $testUser;
            });

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($testRoleId)
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
            'roleId' => $testRoleId,
        ];
        $dto = RoleAssignToUserDTO::fromArray($arrayData);


        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handlerAssign->handle($request);

        $user = $this->useCase->execute($result);

        $roles = $user->getRoles();
        $this->assertEquals($testRoleName, reset($roles));
    }



}
