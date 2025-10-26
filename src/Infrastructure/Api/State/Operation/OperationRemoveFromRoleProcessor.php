<?php

/**
 * @package    EmailCreateProcessor.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Infrastructure\Api\State\Operation;

use ApiPlatform\Metadata\Operation as HTTPOperation;
use ApiPlatform\State\ProcessorInterface;
use Slcorp\RoleModelBundle\Application\DTO\OperationRemoveFromRoleDTO;
use Slcorp\RoleModelBundle\Application\UseCase\Operation\OperationRemoveFromRole;
use Slcorp\RoleModelBundle\Domain\Entity\Role;

/**
 * @template OperationRemoveFromRoleDTO
 * @template Role
 * @implements ProcessorInterface<OperationRemoveFromRoleDTO, Role>
 */
readonly class OperationRemoveFromRoleProcessor implements ProcessorInterface
{
    public function __construct(private OperationRemoveFromRole $create)
    {
    }

    /**
     * @param OperationRemoveFromRoleDTO $data
     * @param HTTPOperation $operation
     * @param array $uriVariables
     * @param array $context
     * @return Role
     */
    public function process(mixed $data, HTTPOperation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->create->execute($data);
    }
}
