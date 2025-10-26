<?php

/**
 * @package    UseCase.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\integration\Application\Service\User;

use Slcorp\RoleModelBundle\Application\DTO\RoleAssignToUserDTO;
use Slcorp\RoleModelBundle\Application\DTO\RoleRemoveFromUserDTOName;
use Slcorp\RoleModelBundle\Application\Service\Operation\OperationService;
use Slcorp\RoleModelBundle\Application\UseCase\Role\RoleAssignToUser;
use Slcorp\RoleModelBundle\Application\UseCase\Role\RoleRemoveFromUser;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\RoleAssignRequestHandler;
use Slcorp\RoleModelBundle\Tests\UseCaseTest;
use Symfony\Component\HttpFoundation\Request;

class HasCapabilityTest extends UseCaseTest
{
    protected const ENABLE_TRANSACTION = true;
    private RoleAssignRequestHandler $handlerAssign;
    private RoleAssignToUser $assignRoleToUserUseCase;
    private RoleRemoveFromUser $removeRoleFromUserUseCase;
    private OperationService $operationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handlerAssign = new RoleAssignRequestHandler($this->serializer);
        $this->assignRoleToUserUseCase = $this->container->get(RoleAssignToUser::class);
        $this->removeRoleFromUserUseCase = $this->container->get(RoleRemoveFromUser::class);
        $this->operationService = $this->container->get(OperationService::class);
    }

    public function testCheckCapabilities(): void
    {
        $userName = 'john_doe_test@mail.ru';
        $user = $this->createTempUserInDb(
            [
                'email' => $userName,
                'firstname' => 'John',
                'lastname' => 'Doe',
                'ad_login' => 'TEst1',
            ]
        );

        $operationRoot = $this->createTempOperationInDb(
            [
                'code' => 'root',
                'name' => 'root',
                'comment' => 'root',
            ]
        );

        $operationMain = $this->createTempOperationInDb(
            [
                'code' => 'edit_report',
                'name' => 'Редактирование отчетов',
                'comment' => 'Редактирование отчетов',
            ]
        );

        $operationChildren = $this->createTempOperationInDb(
            [
                'code' => 'edit_report_user',
                'name' => 'Редактирование отчета USER',
                'comment' => 'Редактирование отчета USER',
                'parentId' => $operationMain->getId(),
            ]
        );

        $operationSubChildren = $this->createTempOperationInDb(
            [
                'code' => 'edit_report_user_sub',
                'name' => 'Редактирование отчета SUB USER',
                'comment' => 'Редактирование отчета SUB USER',
                'parentId' => $operationChildren->getId(),
            ]
        );

        $roleAdmin = $this->createTempRoleInDb(
            [
                'name' => 'ROLE_ADMIN',
                'description' => 'Administrator',
            ]
        );

        $roleUser = $this->createTempRoleInDb(
            [
                'name' => 'ROLE_USER',
                'description' => 'User',
            ]
        );

        $this->operationService->addOperationToRole($operationMain, $roleAdmin);
        $this->operationService->addOperationToRole($operationMain, $roleUser);


        $role = $roleAdmin;
        $arrayData = [
            'userId' => $user->getId(),
            'roleId' => $role->getId(),
        ];
        $dto = RoleAssignToUserDTO::fromArray($arrayData);


        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handlerAssign->handle($request);

        $user = $this->assignRoleToUserUseCase->execute($result);
        $roles = $user->getRoles();
        $this->assertEquals($role->getName(), reset($roles));
        $this->assertEquals($user->getUsername(), $userName);
        $this->assertEquals(1, $roleUser->getOperations()->count());

        $mainCapability = $user->hasCapability($operationMain);
        $childCapability = $user->hasCapability($operationChildren);
        $rootCapability = $user->hasCapability($operationRoot);
        $subCapability = $user->hasCapability($operationSubChildren);

        $this->assertTrue($mainCapability);
        $this->assertTrue($childCapability);
        $this->assertTrue($subCapability);
        $this->assertTrue($rootCapability);


        $arrayData = [
            'userId' => $user->getId(),
            'roleName' => $role->getName(),
        ];
        $dto = RoleRemoveFromUserDTOName::fromArray($arrayData);
        $this->removeRoleFromUserUseCase->executeFromName($dto);

        $mainCapability = $user->hasCapability($operationMain);
        $childCapability = $user->hasCapability($operationChildren);
        $rootCapability = $user->hasCapability($operationRoot);
        $subCapability = $user->hasCapability($operationSubChildren);

        $this->assertFalse($mainCapability);
        $this->assertFalse($childCapability);
        $this->assertFalse($rootCapability);
        $this->assertFalse($subCapability);


        $role = $roleUser;
        $arrayData = [
            'userId' => $user->getId(),
            'roleId' => $role->getId(),
        ];
        $dto = RoleAssignToUserDTO::fromArray($arrayData);


        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handlerAssign->handle($request);

        $user = $this->assignRoleToUserUseCase->execute($result);

        $mainCapability = $user->hasCapability($operationMain);
        $childCapability = $user->hasCapability($operationChildren);
        $rootCapability = $user->hasCapability($operationRoot);
        $subCapability = $user->hasCapability($operationSubChildren);

        //        /**@var OperationRepositoryInterface $repo */
        //        $this->operationService->flatOperationsList($user);

        $this->assertTrue($mainCapability);
        $this->assertTrue($childCapability);
        $this->assertTrue($subCapability);
        $this->assertFalse($rootCapability);
    }


}
