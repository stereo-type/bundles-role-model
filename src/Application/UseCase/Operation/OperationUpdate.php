<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\UseCase\Operation;

use Slcorp\CoreBundle\Application\Service\Validator\ValidatorDTOInterface;
use Slcorp\RoleModelBundle\Application\DTO\OperationCreateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\Service\Operation\OperationService;
use Slcorp\RoleModelBundle\Domain\Entity\Operation;

readonly class OperationUpdate
{
    public function __construct(
        private OperationService $service,
        private ValidatorDTOInterface $validationService,
    ) {
    }

    public function execute(OperationCreateDTO $dto, int $id, bool $flush = true): Operation
    {
        $errors = $this->validationService->validateDTO($dto, partial: true);
        if ($errors->count() > 0) {
            throw BundleException::validationErrors($errors);
        }

        $instance = $this->service->find($id);
        if (!$instance) {
            throw BundleException::operationNotFound($id);
        }

        return $this->service->update($dto, $instance, $flush);
    }
}
