<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Presentation\Controller;

use Slcorp\RoleModelBundle\Application\UseCase\Operation\OperationCreate;
use Slcorp\RoleModelBundle\Application\UseCase\Operation\OperationDelete;
use Slcorp\RoleModelBundle\Application\UseCase\Operation\OperationUpdate;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\OperationCreateRequestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/role-model-bundle/operation')]
class OperationController extends AbstractController
{
    use ResponseTrait;
    public function __construct(
        private readonly OperationCreate $create,
        private readonly OperationUpdate $update,
        private readonly OperationDelete $delete,
        private readonly OperationCreateRequestHandler $requestHandler,
    ) {
    }

    #[Route('/create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return $this->getResponse(function () use ($request) {
            $dto = $this->requestHandler->handle($request);
            $this->create->execute($dto);
            return ['message' => 'Role created successfully'];
        });
    }

    #[Route('/update/{id}', methods: ['POST'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->getResponse(function () use ($request, $id) {
            $dto = $this->requestHandler->handle($request);
            $this->update->execute($dto, $id);
            return ['message' => 'Role updated successfully'];
        });
    }

    #[Route('/delete/{id}', methods: ['POST'])]
    public function delete(int $id): JsonResponse
    {
        return $this->getResponse(function () use ($id) {
            $this->delete->execute($id);
            return ['message' => 'Role delete successfully'];
        });
    }
}
