<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Presentation\Controller;

use Slcorp\RoleModelBundle\Application\UseCase\Role\RoleCreate;
use Slcorp\RoleModelBundle\Application\UseCase\Role\RoleDelete;
use Slcorp\RoleModelBundle\Application\UseCase\Role\RoleUpdate;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\RoleCreateRequestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/role-model-bundle/role')]
class RoleController extends AbstractController
{
    use ResponseTrait;
    public function __construct(
        private readonly RoleCreate $create,
        private readonly RoleUpdate $update,
        private readonly RoleDelete $delete,
        private readonly RoleCreateRequestHandler $requestHandler,
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
