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
use Slcorp\RoleModelBundle\Application\UseCase\Operation\OperationCreate;

/**
 * @template OperationCreateDTO
 * @template Operation
 * @implements ProcessorInterface<OperationCreateDTO, Operation>
 */
readonly class OperationCreateProcessor implements ProcessorInterface
{
    public function __construct(private OperationCreate $create)
    {
    }

    /**
     * @param OperationCreateDTO $data
     * @param HTTPOperation $operation
     * @param array $uriVariables
     * @param array $context
     * @return Operation
     */
    public function process(mixed $data, HTTPOperation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->create->execute($data);
    }
}
