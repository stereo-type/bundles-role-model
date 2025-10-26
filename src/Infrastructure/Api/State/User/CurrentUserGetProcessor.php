<?php

namespace Slcorp\RoleModelBundle\Infrastructure\Api\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\PartialPaginatorInterface;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\State\ProviderInterface;
use Exception;
use Slcorp\RoleModelBundle\Domain\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @template mixed
 * @template User
 * @implements ProcessorInterface<mixed, User>
 *
 * @template T of object
 * @implements ProviderInterface<T>
 */
readonly class CurrentUserGetProcessor implements ProcessorInterface, ProviderInterface
{
    public function __construct(private readonly Security $security)
    {
    }


    /**
     * @param mixed $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     *
     * @return User
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->security->getUser();
    }

    /**
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return T|PartialPaginatorInterface<T>|iterable<T>|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->security->getUser();
    }
}
