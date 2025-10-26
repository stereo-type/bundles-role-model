<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Presentation\Controller;

use Slcorp\RoleModelBundle\Application\UseCase\Email\EmailCreate;
use Slcorp\RoleModelBundle\Application\UseCase\Email\EmailDelete;
use Slcorp\RoleModelBundle\Application\UseCase\Email\EmailUpdate;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\EmailCreateRequestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/role-model-bundle/user')]
class EmailController extends AbstractController
{
    use ResponseTrait;

    public function __construct(
        private readonly EmailCreateRequestHandler $requestHandler,
        private readonly EmailCreate $create,
        private readonly EmailUpdate $update,
        private readonly EmailDelete $delete,
    ) {
    }

    #[Route('/create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return $this->getResponse(function () use ($request) {
            $dto = $this->requestHandler->handle($request);
            $this->create->execute($dto);
            return ['message' => 'Email created successfully'];
        });
    }

    #[Route('/update/{id}', methods: ['POST'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->getResponse(function () use ($request, $id) {
            $dto = $this->requestHandler->handle($request);
            $this->update->execute($dto, $id);
            return ['message' => 'Email updated successfully'];
        });
    }

    #[Route('/delete/{id}', methods: ['POST'])]
    public function delete(int $id): JsonResponse
    {
        return $this->getResponse(function () use ($id) {
            $this->delete->execute($id);
            return ['message' => 'Email delete successfully'];
        });
    }

    #[Route('/test', methods: ['GET'])]
    public function test(Request $request, ParameterBagInterface $parameterBag): JsonResponse
    {
        $d = $parameterBag->get('slcorp_role_model.secret_key');

        return $this->json(['test' => $d]);
    }
}
