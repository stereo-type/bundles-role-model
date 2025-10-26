<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\Role;

use Slcorp\RoleModelBundle\Application\DTO\RoleCreateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\UseCase\Role\RoleUpdate;
use Slcorp\RoleModelBundle\Domain\Entity\Role;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\RoleCreateRequestHandler;
use Symfony\Component\HttpFoundation\Request;

class RoleUpdateTest extends RoleTest
{
    private RoleUpdate $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = new RoleCreateRequestHandler($this->serializer);
        $this->useCase = new RoleUpdate($this->service, $this->validationService);
    }

    public function testRoleNotFoundException(): void
    {
        $arrayData = [
            'name' => 'ROLE_AD',
        ];

        $id = -1;
        $dto = RoleCreateDTO::fromArray($arrayData);

        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);

        $this->expectExceptionObject(BundleException::roleNotFound($id));

        $this->useCase->execute($result, $id);
    }

    public function testRoleUpdateSuccessful(): void
    {
        $testName = 'ROLE_AD';
        $testDescription = 'NEW TEST DESCRIPTION';
        $arrayData = [
            'name' => $testName,
            'description' => $testDescription,
        ];

        $id = -1;
        $dto = RoleCreateDTO::fromArray($arrayData);

        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturnCallback(function ($testId) {
                $user = new Role();
                $user->setName('INIT_NAME');
                $user->setDescription(null);
                return $user;
            });

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function ($user) {
                return $user;
            });

        $updatedUser = $this->useCase->execute($result, $id);
        $this->assertEquals($testName, $updatedUser->getName());
        $this->assertEquals($testDescription, $updatedUser->getDescription());
    }

    public function testValidationUpdatesRoleException(): void
    {
        $arrayData = [
            'name' => 'a',
        ];

        $id = -1;
        $dto = RoleCreateDTO::fromArray($arrayData);


        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);

        $this->expectExceptionObject(BundleException::validationErrors($this->validationService->validateDTO($dto)));

        $this->useCase->execute($result, $id);
    }
}
