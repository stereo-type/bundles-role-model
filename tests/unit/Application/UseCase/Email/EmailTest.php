<?php

/**
 * @package    UserTest.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\Email;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Slcorp\RoleModelBundle\Application\DTO\EmailCreateDTO;
use Slcorp\RoleModelBundle\Application\DTO\UserCreateDTO;
use Slcorp\RoleModelBundle\Application\Mapper\EmailMapper;
use Slcorp\RoleModelBundle\Application\Service\Email\EmailService;
use Slcorp\RoleModelBundle\Domain\Repository\EmailRepositoryInterface;
use Slcorp\RoleModelBundle\Domain\Repository\UserRepositoryInterface;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\EmailCreateRequestHandler;
use Slcorp\RoleModelBundle\Tests\UseCaseTest;
use Symfony\Component\HttpFoundation\Request;

class EmailTest extends UseCaseTest
{
    protected EmailCreateRequestHandler $handler;
    protected EmailService $serviceWithMockRepository;
    protected EmailRepositoryInterface & MockObject $mockRepository;
    protected UserRepositoryInterface & MockObject $mockUserRepository;
    protected EmailMapper $mapper;

    /**
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = new EmailMapper();
        $this->mockRepository = $this->createMock(EmailRepositoryInterface::class);
        $this->mockUserRepository = $this->createMock(UserRepositoryInterface::class);
        $this->serviceWithMockRepository = new EmailService($this->mockRepository, $this->mockUserRepository, $this->mapper, $this->dtoMerger);
        $this->handler = new EmailCreateRequestHandler($this->serializer);
    }

    public function testHandleCreatesEmailSuccessfullyMock(): void
    {
        $dto = $this->getEmailDto();
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);
        $this->assertSame($dto->getEmail(), $result->getEmail());
    }

    protected function getEmailDto(): EmailCreateDTO
    {
        $test_user_id = -1;
        $arrayData = [
            'email' => 'john_doe_test@mail.tu',
            'userId' => $test_user_id,
        ];

        return EmailCreateDTO::fromArray($arrayData);
    }


    public function getUserDto(): UserCreateDTO
    {
        $arrayData = [
            'username' => 'john_doe',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'ad_login' => 'TEst',
        ];

        return UserCreateDTO::fromArray($arrayData);
    }

}
