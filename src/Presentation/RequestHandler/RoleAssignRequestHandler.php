<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Presentation\RequestHandler;

use Slcorp\RoleModelBundle\Application\DTO\RoleAssignToUserDTO;
use Slcorp\RoleModelBundle\Application\DTO\RoleAssignToUserDTOName;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

readonly class RoleAssignRequestHandler implements RequestHandlerInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function handle(Request $request): RoleAssignToUserDTO
    {
        return $this->serializer->deserialize($request->getContent(), RoleAssignToUserDTO::class, 'json');
    }

    public function handleForName(Request $request): RoleAssignToUserDTOName
    {
        return $this->serializer->deserialize($request->getContent(), RoleAssignToUserDTOName::class, 'json');
    }
}
