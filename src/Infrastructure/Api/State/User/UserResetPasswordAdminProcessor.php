<?php

namespace Slcorp\RoleModelBundle\Infrastructure\Api\State\User;

use Slcorp\RoleModelBundle\Application\UseCase\User\UserResetPasswordAdmin;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Exception;
use Slcorp\RoleModelBundle\Domain\Entity\User;

/**
 * @template UserResetPasswordAdminDTO
 * @template User
 * @implements ProcessorInterface<UserResetPasswordAdminDTO, User>
 */
readonly class UserResetPasswordAdminProcessor implements ProcessorInterface
{
    public function __construct(private UserResetPasswordAdmin $create)
    {
    }


    /**
     * @param UserResetPasswordAdminDTO $data
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
