<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\Operation;

use Slcorp\RoleModelBundle\Application\DTO\OperationCreateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\UseCase\Operation\OperationCreate;
use Symfony\Component\HttpFoundation\Request;

class OperationCreateTest extends OperationTest
{
    private OperationCreate $useCaseMocked;
    private OperationCreate $useCaseDB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCaseMocked = new OperationCreate($this->serviceWithMockRepository, $this->validationService);
        $this->useCaseDB = $this->container->get(OperationCreate::class);
    }



    public function testExecuteCreatesOperationSuccessfullyMock(): void
    {
        $dto = $this->getOperationDto();
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);

        $this->mockRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function ($user) {
                return clone $user;
            });

        $saved = $this->useCaseMocked->execute($result);

        $this->assertSame($dto->getCode(), $saved->getCode());
        $this->assertSame($dto->getName(), $saved->getName());
        $this->assertSame($dto->getComment(), $saved->getComment());
    }

    public function testValidationCreatesOperationExceptionMock(): void
    {
        $arrayData = [
            'code' => 'code',
        ];
        $dto = OperationCreateDTO::fromArray($arrayData);
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);

        $this->expectExceptionObject(BundleException::validationErrors($this->validationService->validateDTO($dto)));

        $this->useCaseMocked->execute($result);
    }


    public function testHandleCreatesOperationSuccessfullyDB(): void
    {
        $dto = $this->getOperationDto();
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);
        $saved = $this->useCaseDB->execute($result);

        $this->assertSame($dto->getCode(), $saved->getCode());
        $this->assertSame($dto->getName(), $saved->getName());
        $this->assertSame($dto->getComment(), $saved->getComment());
    }

}
