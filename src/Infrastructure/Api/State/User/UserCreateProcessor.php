<?php

namespace Slcorp\RoleModelBundle\Infrastructure\Api\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Exception;
use Slcorp\RoleModelBundle\Application\UseCase\User\UserCreate;
use Slcorp\RoleModelBundle\Domain\Entity\User;

/**
 * @template UserCreateDTO
 * @template User
 * @implements ProcessorInterface<UserCreateDTO, User>
 */
readonly class UserCreateProcessor implements ProcessorInterface
{
    public function __construct(private UserCreate $create)
    {
    }


    /**
     * @param UserCreateDTO $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     *
     * @return User
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->create->execute($data);
    }

}
