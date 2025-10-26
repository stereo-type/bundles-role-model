<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\UseCase\Role;

use Slcorp\RoleModelBundle\Application\DTO\RoleCreateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\Service\Role\RoleService;
use Slcorp\CoreBundle\Application\Service\Validator\ValidatorDTOInterface;
use Slcorp\RoleModelBundle\Domain\Entity\Role;

readonly class RoleCreate
{
    public function __construct(
        private RoleService $service,
        private ValidatorDTOInterface $validationService,
    ) {
    }

    public function execute(RoleCreateDTO $dto): Role
    {
        $errors = $this->validationService->validateDTO($dto);
        if ($errors->count() > 0) {
            throw BundleException::validationErrors($errors);
        }
        return $this->service->create($dto);
    }
}
