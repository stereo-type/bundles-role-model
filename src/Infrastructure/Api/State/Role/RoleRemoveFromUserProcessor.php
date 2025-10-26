<?php

/**
 * @package    EmailCreateProcessor.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Infrastructure\Api\State\Role;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Slcorp\RoleModelBundle\Application\DTO\RoleRemoveFromUserDTO;
use Slcorp\RoleModelBundle\Application\UseCase\Role\RoleRemoveFromUser;
use Slcorp\RoleModelBundle\Domain\Entity\User;

/**
 * @template RoleRemoveFromUserDTO
 * @template User
 * @implements ProcessorInterface<RoleRemoveFromUserDTO, User>
 */
readonly class RoleRemoveFromUserProcessor implements ProcessorInterface
{
    public function __construct(private RoleRemoveFromUser $create)
    {
    }

    /**
     * @param RoleRemoveFromUserDTO $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return User
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->create->execute($data);
    }
}
