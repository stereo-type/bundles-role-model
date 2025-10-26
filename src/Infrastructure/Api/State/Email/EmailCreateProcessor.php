<?php

/**
 * @package    EmailCreateProcessor.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Infrastructure\Api\State\Email;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Slcorp\RoleModelBundle\Application\DTO\EmailCreateDTO;
use Slcorp\RoleModelBundle\Application\UseCase\Email\EmailCreate;
use Slcorp\RoleModelBundle\Domain\Entity\Email;

/**
 * @template EmailCreateDTO
 * @template Email
 * @implements ProcessorInterface<EmailCreateDTO, Email>
 */
readonly class EmailCreateProcessor implements ProcessorInterface
{
    public function __construct(private EmailCreate $create)
    {
    }

    /**
     * @param EmailCreateDTO $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return Email
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->create->execute($data);
    }
}
