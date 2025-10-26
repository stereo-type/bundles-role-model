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
use Slcorp\RoleModelBundle\Application\UseCase\Operation\OperationUpdate;

/**
 * @template OperationCreateDTO
 * @template Operation
 * @implements ProcessorInterface<OperationCreateDTO, Operation>
 */
readonly class OperationUpdateProcessor implements ProcessorInterface
{
    public function __construct(private OperationUpdate $create)
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
        $id = (int)$uriVariables['id'];
        return $this->create->execute($data, $id);
    }
}
