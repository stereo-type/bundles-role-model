<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\Operation;

use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\UseCase\Operation\OperationDelete;

class OperationDeleteTest extends OperationTest
{
    private OperationDelete $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new OperationDelete($this->serviceWithMockRepository, $this->mockEntityManager);
    }

    public function testHandleDeleteOperationException(): void
    {
        $testId = -1;
        $this->expectExceptionObject(BundleException::operationNotFound($testId));
        $this->useCase->execute($testId);
    }

    public function testHandleDeleteOperation(): void
    {
        $testId = -1;
        $testOperation = $this->getTestOperation();

        $this->mockRepository
            ->expects($this->once())
            ->method('find')
            ->with($testId)
            ->willReturnCallback(function ($testId) use ($testOperation) {
                return clone $testOperation;
            });

        $result = $this->useCase->execute($testId);
        $this->assertTrue($result);
    }
}
