<?php

namespace Slcorp\RoleModelBundle\Infrastructure\Api\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Slcorp\RoleModelBundle\Application\UseCase\User\UserRestore;

/**
 * @template UserUpdateDTO
 * @template bool
 * @implements ProcessorInterface<UserUpdateDTO, bool>
 */
readonly class UserRestoreProcessor implements ProcessorInterface
{
    public function __construct(private UserRestore $create)
    {
    }


    /**
     * @param UserUpdateDTO $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     *
     * @return bool
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $id = (int)$uriVariables['id'];
        return $this->create->execute($id);
    }

}
