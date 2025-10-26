<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\UseCase\User;

use Slcorp\RoleModelBundle\Application\DTO\UserUpdateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\Service\User\UserService;
use Slcorp\CoreBundle\Application\Service\Validator\ValidatorDTOInterface;
use Slcorp\RoleModelBundle\Domain\Entity\User;

readonly class UserUpdate
{
    public function __construct(
        private UserService $service,
        private ValidatorDTOInterface $validationService
    ) {
    }

    public function execute(UserUpdateDTO $dto, int $id, bool $flush = true): User
    {
        $errors = $this->validationService->validateDTO($dto, partial: true);
        if ($errors->count() > 0) {
            throw BundleException::validationErrors($errors);
        }

        $user = $this->service->getUserById($id);
        if (!$user) {
            throw BundleException::userNotFound($id);
        }

        return $this->service->update($dto, $user, $flush);
    }
}
