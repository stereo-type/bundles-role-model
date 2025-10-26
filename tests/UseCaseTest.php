<?php

/**
 * @package    UseCase.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests;

use Slcorp\CoreBundle\Infrastructure\Validator\DTOValidationService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Slcorp\RoleModelBundle\Application\DTO\OperationCreateDTO;
use Slcorp\RoleModelBundle\Application\DTO\RoleCreateDTO;
use Slcorp\RoleModelBundle\Application\DTO\UserCreateDTO;
use Slcorp\RoleModelBundle\Application\Service\DTOMerger;
use Slcorp\RoleModelBundle\Application\UseCase\Operation\OperationCreate;
use Slcorp\RoleModelBundle\Application\UseCase\Role\RoleCreate;
use Slcorp\RoleModelBundle\Application\UseCase\User\UserCreate;
use Slcorp\RoleModelBundle\Domain\Entity\Operation;
use Slcorp\RoleModelBundle\Domain\Entity\Role;
use Slcorp\RoleModelBundle\Domain\Entity\User;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\OperationCreateRequestHandler;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\RoleCreateRequestHandler;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\UserCreateRequestHandler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UseCaseTest extends WebTestCase
{
    protected ContainerInterface $container;
    protected DTOValidationService $validationService;
    protected SerializerInterface $serializer;
    protected Connection|null $connection;
    protected DTOMerger $dtoMerger;
    protected EntityManagerInterface & MockObject $mockEntityManager;
    protected EntityManagerInterface $entityManager;

    protected const ENABLE_TRANSACTION = true;

    protected function setUp(): void
    {
        $this->container = static::createClient()->getContainer();
        $this->validationService = new DTOValidationService($this->container->get(ValidatorInterface::class));
        $this->serializer = $this->container->get(SerializerInterface::class);
        $this->connection = $this->container->get('doctrine.dbal.default_connection');
        $this->dtoMerger = new DTOMerger(PropertyAccess::createPropertyAccessor());
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager = $this->container->get(EntityManagerInterface::class);
        if (static::ENABLE_TRANSACTION) {
            $this->connection->beginTransaction();
        }
    }

    public function testInit(): void
    {
        $this->assertInstanceOf(Connection::class, $this->connection);
    }

    protected function createTempUserInDb(?array $data = null): User
    {
        $arrayData = $data ?? [
            'email' => 'john_doe_test@example.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'ad_login' => 'TEst',
        ];

        $dto = UserCreateDTO::fromArray($arrayData);
        $handler = new UserCreateRequestHandler($this->serializer);
        $useCase = $this->container->get(UserCreate::class);
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $userHandle = $handler->handle($request);
        return $useCase->execute($userHandle);
    }

    protected function createTempOperationInDb(?array $data = null): Operation
    {
        $arrayData = $data ?? [
            'code' => 'john_doe_code',
            'name' => 'john_doe_name',
            'comment' => 'john_doe_comment',
        ];

        $dto = OperationCreateDTO::fromArray($arrayData);
        $handler = new OperationCreateRequestHandler($this->serializer);
        $useCase = $this->container->get(OperationCreate::class);
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $userHandle = $handler->handle($request);
        return $useCase->execute($userHandle);
    }

    protected function createTempRoleInDb(?array $data = null): Role
    {
        $arrayData = $data ?? [
            'name' => 'ROLE_ADMIN',
            'description' => 'Administrator',
        ];

        $dto = RoleCreateDTO::fromArray($arrayData);
        $handler = new RoleCreateRequestHandler($this->serializer);
        $useCase = $this->container->get(RoleCreate::class);
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $userHandle = $handler->handle($request);
        return $useCase->execute($userHandle);
    }


    protected function tearDown(): void
    {
        if (static::ENABLE_TRANSACTION) {
            $this->connection->rollBack();
        }

        parent::tearDown();
    }


}
