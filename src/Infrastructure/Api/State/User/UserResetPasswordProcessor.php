<?php

namespace Slcorp\RoleModelBundle\Infrastructure\Api\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Exception;
use Slcorp\RoleModelBundle\Application\DTO\UserResetPasswordDTO;
use Slcorp\RoleModelBundle\Application\UseCase\User\UserResetPassword;
use Slcorp\RoleModelBundle\Domain\Entity\User;

/**
 * @template UserResetPasswordDTO
 * @template User
 * @implements ProcessorInterface<UserResetPasswordDTO, User>
 */
readonly class UserResetPasswordProcessor implements ProcessorInterface
{
    public function __construct(private UserResetPassword $create)
    {
    }


    /**
     * @param UserResetPasswordDTO $data
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
