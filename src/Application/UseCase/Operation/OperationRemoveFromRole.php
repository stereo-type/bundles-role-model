<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\UseCase\Operation;

use Slcorp\CoreBundle\Application\Service\Validator\ValidatorDTOInterface;
use Slcorp\RoleModelBundle\Application\DTO\OperationRemoveFromRoleDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\Service\Operation\OperationService;
use Slcorp\RoleModelBundle\Domain\Entity\Role;

readonly class OperationRemoveFromRole
{
    public function __construct(
        private OperationService $service,
        private ValidatorDTOInterface $validationService,
    ) {
    }

    public function execute(OperationRemoveFromRoleDTO $dto, bool $flush = true): Role
    {
        $errors = $this->validationService->validateDTO($dto);
        if ($errors->count() > 0) {
            throw BundleException::validationErrors($errors);
        }
        return $this->service->removeOperationFromRoleId($dto->operation, $dto->roleId);
    }
}
