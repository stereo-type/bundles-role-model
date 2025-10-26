<?php

/**
 * @package    UserTest.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\User;

use Slcorp\RoleModelBundle\Application\Service\User\GidService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Slcorp\RoleModelBundle\Application\DTO\UserCreateDTO;
use Slcorp\RoleModelBundle\Application\Mapper\UserMapper;
use Slcorp\RoleModelBundle\Application\Service\User\UserService;
use Slcorp\RoleModelBundle\Domain\Repository\EmailRepositoryInterface;
use Slcorp\RoleModelBundle\Domain\Repository\UserRepositoryInterface;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\UserCreateRequestHandler;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\UserUpdateRequestHandler;
use Slcorp\RoleModelBundle\Tests\UseCaseTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserTest extends UseCaseTest
{
    protected UserCreateRequestHandler $handler;
    protected UserUpdateRequestHandler $handlerUpdate;
    protected UserService $service;
    protected UserRepositoryInterface & MockObject $repository;
    protected EmailRepositoryInterface & MockObject $repositoryEmail;

    /**
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->repositoryEmail = $this->createMock(EmailRepositoryInterface::class);
        $this->service = new UserService(
            $this->repository,
            new UserMapper(),
            $this->dtoMerger,
            $this->container->get(UserPasswordHasherInterface::class),
            $this->repositoryEmail,
            $this->container->get(GidService::class),
        );
        $this->handler = new UserCreateRequestHandler($this->serializer);
        $this->handlerUpdate = new UserUpdateRequestHandler($this->serializer);
    }

    public function testHandleCreatesUserSuccessfullyMock(): void
    {
        $dto = $this->getUserDto();
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);

        $this->assertSame($dto->getEmail(), $result->getEmail());
        $this->assertSame($dto->getLastname(), $result->getLastname());
        $this->assertSame($dto->getFirstname(), $result->getFirstname());
    }

    public function getUserDto(): UserCreateDTO
    {
        $arrayData = [
            'email' => 'john_doe_test_dto@mail.ru',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'ad_login' => 'TEst',
        ];

        return UserCreateDTO::fromArray($arrayData);
    }


}
