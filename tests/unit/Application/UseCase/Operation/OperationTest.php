<?php

/**
 * @package    UserTest.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\Operation;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Slcorp\RoleModelBundle\Application\DTO\OperationCreateDTO;
use Slcorp\RoleModelBundle\Application\Mapper\OperationMapper;
use Slcorp\RoleModelBundle\Application\Service\Operation\OperationService;
use Slcorp\RoleModelBundle\Domain\Entity\Operation;
use Slcorp\RoleModelBundle\Domain\Repository\OperationRepositoryInterface;
use Slcorp\RoleModelBundle\Domain\Repository\RoleRepositoryInterface;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\OperationCreateRequestHandler;
use Slcorp\RoleModelBundle\Tests\UseCaseTest;
use Symfony\Component\HttpFoundation\Request;

class OperationTest extends UseCaseTest
{
    protected OperationCreateRequestHandler $handler;
    protected OperationService $serviceWithMockRepository;
    protected OperationRepositoryInterface & MockObject $mockRepository;
    protected RoleRepositoryInterface & MockObject $mockRoleRepository;
    protected OperationMapper $mapper;

    /**
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRepository = $this->createMock(OperationRepositoryInterface::class);
        $this->mockRoleRepository = $this->createMock(RoleRepositoryInterface::class);
        $this->mapper = new OperationMapper();
        $this->serviceWithMockRepository = new OperationService($this->mockRepository, $this->mockRoleRepository, $this->mapper, $this->dtoMerger);
        $this->handler = new OperationCreateRequestHandler($this->serializer);
    }

    public function testHandleCreatesOperationSuccessfullyMock(): void
    {
        $dto = $this->getOperationDto();
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);

        $this->assertSame($dto->getCode(), $result->getCode());
        $this->assertSame($dto->getName(), $result->getName());
        $this->assertSame($dto->getComment(), $result->getComment());
    }

    public function getOperationDto(): OperationCreateDTO
    {
        $arrayData = [
            'code' => 'john_doe_code',
            'name' => 'john_doe_name',
            'comment' => 'john_doe_comment',
        ];

        return OperationCreateDTO::fromArray($arrayData);
    }

    public function getTestOperation(): Operation
    {
        $testCode = 'code';
        $testName = 'name';
        $testComment = 'comment';

        $testOperation = new Operation();
        $testOperation->setCode($testCode);
        $testOperation->setName($testName);
        $testOperation->setComment($testComment);
        return $testOperation;
    }

}
