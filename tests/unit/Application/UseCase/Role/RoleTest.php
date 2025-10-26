<?php

/**
 * @package    RoleTest.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\Role;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Slcorp\RoleModelBundle\Application\DTO\RoleCreateDTO;
use Slcorp\RoleModelBundle\Application\Mapper\RoleMapper;
use Slcorp\RoleModelBundle\Application\Service\DTOMerger;
use Slcorp\RoleModelBundle\Application\Service\Role\RoleService;
use Slcorp\RoleModelBundle\Domain\Repository\RoleRepositoryInterface;
use Slcorp\RoleModelBundle\Domain\Repository\UserRepositoryInterface;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\RoleCreateRequestHandler;
use Slcorp\RoleModelBundle\Tests\UseCaseTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

class RoleTest extends UseCaseTest
{
    protected RoleRepositoryInterface & MockObject $repository;
    protected UserRepositoryInterface & MockObject $userRepository;
    protected RoleService $service;
    protected RoleCreateRequestHandler $handler;

    /**
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(RoleRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->service = new RoleService($this->repository, $this->userRepository, new RoleMapper(), new DTOMerger(PropertyAccess::createPropertyAccessor()));
        $this->handler = new RoleCreateRequestHandler($this->serializer);
    }

    public function testHandleCreatesRoleSuccessfullyMock(): void
    {
        $dto = $this->getRoleDto();
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);

        $this->assertSame($dto->getName(), $result->getName());
        $this->assertSame($dto->getDescription(), $result->getDescription());
    }

    public function getRoleDto(): RoleCreateDTO
    {
        $arrayData = [
            'name' => 'ROLE_ADMIN',
            'description' => 'Administrator',
        ];

        return RoleCreateDTO::fromArray($arrayData);
    }



}
