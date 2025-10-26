<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\UseCase\User;

use Slcorp\RoleModelBundle\Application\DTO\UserResetPasswordDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\Service\User\UserService;
use Slcorp\CoreBundle\Application\Service\Validator\ValidatorDTOInterface;
use Slcorp\RoleModelBundle\Domain\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserResetPassword
{
    public function __construct(
        private UserService $service,
        private ValidatorDTOInterface $validationService,
        private UserPasswordHasherInterface $userPasswordEncoder
    ) {
    }

    public function execute(UserResetPasswordDTO $dto, int $id, bool $flush = true): User
    {
        $errors = $this->validationService->validateDTO($dto, partial: true);
        if ($errors->count() > 0) {
            throw BundleException::validationErrors($errors);
        }

        $user = $this->service->getUserById($id);
        if (!$user) {
            throw BundleException::userNotFound($id);
        }

        $plainPassword = $dto->getPlainPassword();
        $oldPassword = $dto->getOldPassword();
        if (!$this->userPasswordEncoder->isPasswordValid($user, $oldPassword)) {
            throw new AccessDeniedHttpException('Не правильный старый пароль');
        }


        return $this->service->resetPassword($plainPassword, $user, $flush);
    }
}
