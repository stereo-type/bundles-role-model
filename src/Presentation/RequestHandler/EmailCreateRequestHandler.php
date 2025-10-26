<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Presentation\RequestHandler;

use Slcorp\RoleModelBundle\Application\DTO\EmailCreateDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class EmailCreateRequestHandler implements RequestHandlerInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function handle(Request $request): EmailCreateDTO
    {
        return $this->serializer->deserialize($request->getContent(), EmailCreateDTO::class, 'json');
    }
}
