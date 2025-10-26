<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\User;

use Slcorp\RoleModelBundle\Application\DTO\UserCreateDTO;
use Slcorp\RoleModelBundle\Application\DTO\UserUpdateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\UseCase\User\UserUpdate;
use Slcorp\RoleModelBundle\Domain\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class UserUpdateTest extends UserTest
{
    private UserUpdate $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new UserUpdate($this->service, $this->validationService);
    }

    public function testUserNotFoundException(): void
    {
        $arrayData = [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
        ];

        $id = -1;
        $dto = UserUpdateDTO::fromArray($arrayData);

        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handlerUpdate->handle($request);

        $this->expectExceptionObject(BundleException::userNotFound($id));

        $this->useCase->execute($result, $id);
    }

    public function testUserUpdateSuccessful(): void
    {
        $testLastname = 'Smith';
        $arrayData = [
            'email222' => 'john@doe.com',
            'lastname' => $testLastname
        ];

        $id = -1;
        $dto = UserCreateDTO::fromArray($arrayData);

        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handlerUpdate->handle($request);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturnCallback(function ($testId) {
                $user = new User();
                $user->setLastname('Doe');
                return $user;
            });

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function ($user) {
                return $user;
            });
        $updatedUser = $this->useCase->execute($result, $id);
        $this->assertEquals($testLastname, $updatedUser->getLastname());
    }

    public function testValidationUpdatesUserException(): void
    {
        $arrayData = [
            'lastname' => 'Sm'
        ];

        $id = -1;
        $dto = UserUpdateDTO::fromArray($arrayData);

        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handlerUpdate->handle($request);

        $this->expectExceptionObject(BundleException::validationErrors($this->validationService->validateDTO($dto, partial: true)));
        $this->useCase->execute($result, $id);
    }
}
