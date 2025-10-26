<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\UseCase\User;

use Slcorp\RoleModelBundle\Application\DTO\UserResetPasswordAdminDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\Service\User\UserService;
use Slcorp\CoreBundle\Application\Service\Validator\ValidatorDTOInterface;
use Slcorp\RoleModelBundle\Domain\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;

readonly class UserResetPasswordAdmin
{
    public function __construct(
        private UserService $service,
        private ValidatorDTOInterface $validationService,
        private Security $security
    ) {
    }

    public function execute(UserResetPasswordAdminDTO $dto, int $id, bool $flush = true): User
    {
        $errors = $this->validationService->validateDTO($dto);
        if ($errors->count() > 0) {
            throw BundleException::validationErrors($errors);
        }

        if (!in_array('ROLE_ADMIN', $this->security->getUser()?->getRoles() ?? [], true)) {
            throw BundleException::customMessage('Только администратор может менять пароль, без указания старого пароля', code: Response::HTTP_FORBIDDEN);
        }

        $user = $this->service->getUserById($id);
        if (!$user) {
            throw BundleException::userNotFound($id);
        }

        $plainPassword = $dto->getPlainPassword();

        return $this->service->resetPassword($plainPassword, $user, $flush);
    }
}
