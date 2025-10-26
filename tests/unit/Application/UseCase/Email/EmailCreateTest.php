<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\Email;

use Slcorp\RoleModelBundle\Application\DTO\EmailCreateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\UseCase\Email\EmailCreate;
use Slcorp\RoleModelBundle\Domain\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class EmailCreateTest extends EmailTest
{
    private EmailCreate $useCaseMocked;
    private EmailCreate $useCaseDB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCaseMocked = new EmailCreate($this->serviceWithMockRepository, $this->validationService);
        $this->useCaseDB = $this->container->get(EmailCreate::class);
    }

    public function testExecuteCreatesEmailSuccessfullyMock(): void
    {
        $dto = $this->getEmailDto();
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);

        $testUser = new User();

        $this->mockUserRepository
            ->expects($this->once())
            ->method('find')
            ->with($dto->getUserId())
            ->willReturn($testUser);

        $saved = $this->useCaseMocked->execute($result);

        $this->assertSame($saved->getEmail(), $saved->getEmail());
    }

    public function testValidationCreatesEmailExceptionMock(): void
    {
        $arrayData = [
            'email' => 'incorrect_email',
        ];
        $dto = EmailCreateDTO::fromArray($arrayData);
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);

        $this->expectExceptionObject(BundleException::validationErrors($this->validationService->validateDTO($dto)));

        $this->useCaseMocked->execute($result);
    }

    public function testHandleCreatesEmailSuccessfullyDB(): void
    {
        $createdUser = $this->createTempUserInDb();
        $arrayData = [
            'email' => 'john_doe_test@mail.tu',
            'userId' => $createdUser->getId(),
        ];

        $dto = EmailCreateDTO::fromArray($arrayData);
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $emailHandle = $this->handler->handle($request);
        $createdEmail = $this->useCaseDB->execute($emailHandle);

        $this->assertEquals($arrayData['email'], $createdEmail->getEmail());
        $this->assertEquals($createdUser->getLastname(), $createdEmail->getUser()->getLastname());
    }

    public function testHandleCreatesEmailDuplicateFailure(): void
    {
        $createdUser = $this->createTempUserInDb();
        $arrayData = [
            'email' => 'john_doe_test@mail.tu',
            'userId' => $createdUser->getId(),
        ];

        $dto = EmailCreateDTO::fromArray($arrayData);
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $emailHandle = $this->handler->handle($request);
        $createdEmail = $this->useCaseDB->execute($emailHandle);

        $this->expectExceptionObject(BundleException::validationErrors($this->validationService->validateDTO($dto)));
        $this->useCaseDB->execute($emailHandle);

        $this->assertEquals($arrayData['email'], $createdEmail->getEmail());
    }

}
