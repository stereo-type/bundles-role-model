<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\Role;

use Slcorp\RoleModelBundle\Application\DTO\RoleCreateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\UseCase\Role\RoleCreate;
use Symfony\Component\HttpFoundation\Request;

class RoleCreateTest extends RoleTest
{
    private RoleCreate $useCase;
    private RoleCreate $useCaseDB;


    protected function setUp(): void
    {
        parent::setUp();

        $this->useCase = new RoleCreate($this->service, $this->validationService);
        $this->useCaseDB = $this->container->get(RoleCreate::class);
    }

    public function testValidationCreatesRoleException(): void
    {
        $arrayData = [
            'name2' => 'John Doe',
        ];
        $dto = RoleCreateDTO::fromArray($arrayData);
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);

        $this->expectExceptionObject(BundleException::validationErrors($this->validationService->validateDTO($dto)));

        $this->useCase->execute($result);
    }


    public function testHandleCreatesUserSuccessfullyDB(): void
    {
        $dto = $this->getRoleDto();
        $request = new Request([], [], [], [], [], [], $dto->toJson());
        $result = $this->handler->handle($request);
        $saved = $this->useCaseDB->execute($result);

        $this->assertSame($dto->getName(), $saved->getName());
        $this->assertSame($dto->getDescription(), $saved->getDescription());
    }
}
