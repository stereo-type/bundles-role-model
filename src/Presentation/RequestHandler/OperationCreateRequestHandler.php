<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Presentation\RequestHandler;

use Slcorp\RoleModelBundle\Application\DTO\OperationCreateDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class OperationCreateRequestHandler implements RequestHandlerInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function handle(Request $request): OperationCreateDTO
    {
        return $this->serializer->deserialize($request->getContent(), OperationCreateDTO::class, 'json');
    }
}
