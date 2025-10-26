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
use Slcorp\RoleModelBundle\Application\UseCase\Role\RoleUpdate;

/**
 * @template RoleCreateDTO
 * @template Role
 * @implements ProcessorInterface<RoleCreateDTO, Role>
 */
readonly class RoleUpdateProcessor implements ProcessorInterface
{
    public function __construct(private RoleUpdate $create)
    {
    }

    /**
     * @param RoleCreateDTO $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return Role
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $id = (int)$uriVariables['id'];
        return $this->create->execute($data, $id);
    }
}
