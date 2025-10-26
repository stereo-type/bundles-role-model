<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\UseCase\User;

use Slcorp\RoleModelBundle\Application\DTO\UserCreateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\Service\Email\EmailService;
use Slcorp\RoleModelBundle\Application\Service\User\UserService;
use Slcorp\RoleModelBundle\Domain\Entity\User;
use Slcorp\RoleModelBundle\Infrastructure\Validator\UserValidationService;

readonly class UserCreate
{
    public function __construct(
        private UserService $userService,
        private EmailService $emailService,
        private UserValidationService $validationService,
    ) {
    }

    public function execute(UserCreateDTO $dto, bool $flush = true): User
    {
        $errors = $this->validationService->validateDTO($dto);
        if ($errors->count() > 0) {
            throw BundleException::validationErrors($errors);
        }

        $user = $this->userService->create($dto, flush: false);
        $this->emailService->createForUser($dto->getEmail(), $user, flush: $flush);

        return $user;
    }
}
