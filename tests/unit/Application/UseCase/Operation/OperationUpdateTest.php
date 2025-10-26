<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\Operation;

use Slcorp\RoleModelBundle\Application\DTO\OperationCreateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\UseCase\Operation\OperationUpdate;
use Symfony\Component\HttpFoundation\Request;

class OperationUpdateTest extends OperationTest
{
    private OperationUpdate $useCaseMocked;
    //    private OperationUpdate $useCaseDB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCaseMocked = new OperationUpdate($this->serviceWithMockRepository, $this->validationService);
        //        $this->useCaseDB = $this->container->get(OperationUpdate::class);
    }

    public function testOperationFoundException(): void
    {
        $id = -1;
        $this->expectExceptionObject(BundleException::operationNotFound($id));
        $this->useCaseMocked->execute(new OperationCreateDTO(), $id);
    }

    public function testOperationUpdateSuccessfulMock(): void
    {
        $testName = 'test_operation';
        $testDescription = 'NEW TEST DESCRIPTION';
        $arrayData = [
            'name' => $testName,
            'description' => $testDescription,
        ];

        $id = -1;
        $dto = OperationCreateDTO::fromArray($arrayData);

        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);

        $testOperation = $this->getTestOperation();

        $this->mockRepository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturnCallback(function ($id) use ($testOperation) {
                return clone $testOperation;
            });

        $this->mockRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function ($user) {
                return $user;
            });

        $updatedUser = $this->useCaseMocked->execute($result, $id);
        $this->assertEquals($testName, $updatedUser->getName());
        $this->assertEquals($testDescription, $updatedUser->getDescription());
    }




}
