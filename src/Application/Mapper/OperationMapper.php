<?php

/**
 * @package    UserMapper.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Mapper;

use Slcorp\RoleModelBundle\Application\DTO\OperationCreateDTO;
use Slcorp\RoleModelBundle\Domain\Entity\Operation;
use Slcorp\RoleModelBundle\Domain\Repository\OperationRepositoryInterface;

class OperationMapper
{
    use DtoToEntityTrait;
    public function fromDto(OperationCreateDTO $dto, OperationRepositoryInterface $repository): Operation
    {
        $operation = new Operation();
        $this->mapDtoToEntity($dto, $operation);

        if ($dto->getParentId()) {
            $parentOperation = $repository->find($dto->getParentId());
            $operation->setParent($parentOperation);
        }

        if ($dto->getRootId()) {
            $rootOperation = $repository->find($dto->getRootId());
            $operation->setRoot($rootOperation);
        }

        return $operation;
    }
}
