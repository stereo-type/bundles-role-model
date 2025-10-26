<?php

namespace Slcorp\RoleModelBundle\Infrastructure\Api\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Exception;
use Slcorp\RoleModelBundle\Application\UseCase\User\UserUpdate;
use Slcorp\RoleModelBundle\Domain\Entity\User;

/**
 * @template UserUpdateDTO
 * @template User
 * @implements ProcessorInterface<UserUpdateDTO, User>
 */
readonly class UserUpdateProcessor implements ProcessorInterface
{
    public function __construct(private UserUpdate $create)
    {
    }


    /**
     * @param UserUpdateDTO $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     *
     * @return User
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $id = (int)$uriVariables['id'];
        return $this->create->execute($data, $id);
    }

}
