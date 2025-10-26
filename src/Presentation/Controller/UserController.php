<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Presentation\Controller;

use Slcorp\RoleModelBundle\Application\UseCase\User\UserCreate;
use Slcorp\RoleModelBundle\Application\UseCase\User\UserDelete;
use Slcorp\RoleModelBundle\Application\UseCase\User\UserUpdate;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\UserCreateRequestHandler;
use Slcorp\RoleModelBundle\Presentation\RequestHandler\UserUpdateRequestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/role-model-bundle/user')]
class UserController extends AbstractController
{
    use ResponseTrait;

    public function __construct(
        private readonly UserCreateRequestHandler $requestHandler,
        private readonly UserUpdateRequestHandler $requestUpdateHandler,
        private readonly UserCreate $create,
        private readonly UserUpdate $update,
        private readonly UserDelete $delete,
    ) {
    }

    #[Route('/create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return $this->getResponse(function () use ($request) {
            $dto = $this->requestHandler->handle($request);
            $this->create->execute($dto);
            return ['message' => 'Operation created successfully'];
        });
    }

    #[Route('/update/{id}', methods: ['POST'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->getResponse(function () use ($request, $id) {
            $dto = $this->requestUpdateHandler->handle($request);
            $this->update->execute($dto, $id);
            return ['message' => 'Operation updated successfully'];
        });
    }

    #[Route('/delete/{id}', methods: ['POST'])]
    public function delete(int $id): JsonResponse
    {
        return $this->getResponse(function () use ($id) {
            $this->delete->execute($id);
            return ['message' => 'Operation delete successfully'];
        });
    }
}
