<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Tests\unit\Application\UseCase\Role;

use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\UseCase\Role\RoleDelete;
use Slcorp\RoleModelBundle\Domain\Entity\Role;

class RoleDeleteTest extends RoleTest
{
    private RoleDelete $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new RoleDelete($this->service);
    }

    public function testHandleDeleteUserException(): void
    {
        $testId = -1;
        $this->expectExceptionObject(BundleException::roleNotFound($testId));
        $this->useCase->execute($testId);
    }

    public function testHandleDeleteUser(): void
    {
        $testId = -1;
        $testName = 'ROLE_ADMIN';
        $testDescription = 'Administrator';

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($testId)
            ->willReturnCallback(function ($testId) use ($testName, $testDescription) {
                $role = new Role();
                $role->setName($testName);
                $role->setDescription($testDescription);
                return $role;
            });

        $result = $this->useCase->execute($testId);
        $this->assertTrue($result);
    }


}
