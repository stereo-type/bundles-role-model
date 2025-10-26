<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\User;

use Slcorp\RoleModelBundle\Application\DTO\UserCreateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\Service\Email\EmailService;
use Slcorp\RoleModelBundle\Application\UseCase\User\UserCreate;
use Slcorp\RoleModelBundle\Infrastructure\Validator\UserValidationService;
use Symfony\Component\HttpFoundation\Request;

class UserCreateTest extends UserTest
{
    private UserCreate $useCaseMocked;
    private UserCreate $useCaseDB;

    private UserValidationService $userValidationService;
    private EmailService $emailService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userValidationService = $this->container->get(UserValidationService::class);
        $this->emailService = $this->createMock(EmailService::class);
        $this->useCaseMocked = new UserCreate($this->service, $this->emailService, $this->userValidationService);
        $this->useCaseDB = $this->container->get(UserCreate::class);
    }

    public function testExecuteCreatesUserSuccessfullyMock(): void
    {
        $dto = $this->getUserDto();
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function ($user) {
                return clone $user;
            });

        $saved = $this->useCaseMocked->execute($result);

        $this->assertSame($dto->getEmail(), $saved->getUsername());
        $this->assertSame($dto->getLastname(), $saved->getLastname());
        $this->assertSame($dto->getFirstname(), $saved->getFirstname());
    }

    public function testValidationCreatesUserExceptionMock(): void
    {
        $arrayData = [
            'username' => 'john_doe',
        ];
        $dto = UserCreateDTO::fromArray($arrayData);
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);

        $this->expectExceptionObject(BundleException::validationErrors($this->userValidationService->validateDTO($dto)));

        $this->useCaseMocked->execute($result);
    }


    public function testHandleCreatesUserSuccessfullyDB(): void
    {
        $dto = $this->getUserDto();
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);
        $createdUser = $this->useCaseDB->execute($result);

        $this->assertSame($dto->getEmail(), $createdUser->getUsername());
        $this->assertSame($dto->getLastname(), $createdUser->getLastname());
        $this->assertSame($dto->getFirstname(), $createdUser->getFirstname());
    }

}
