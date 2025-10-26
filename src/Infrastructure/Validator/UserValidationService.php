<?php

/**
 * @package    ${FILE_NAME}
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Infrastructure\Validator;

use Slcorp\CoreBundle\Application\Service\Validator\ValidatorDTOInterface;
use Slcorp\RoleModelBundle\Domain\Repository\EmailRepositoryInterface;
use Slcorp\RoleModelBundle\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserValidationService
{
    public function __construct(
        private ValidatorDTOInterface $validator,
        private UserRepositoryInterface $repository,
        private EmailRepositoryInterface $emailRepository,
    ) {
    }

    public function validateDTO(object $dto, bool $partial = false): ConstraintViolationListInterface
    {
        $result = $this->validator->validateDTO($dto, $partial);
        /**Потому что может быть ошибка ассерта, например не передан емейл и тогда буден необработанная ошибка*/
        if ($result->count() === 0) {
            $userName = $dto->getEmail();
            if ($this->emailRepository->findByEmail($userName)) {
                $violation = new ConstraintViolation(
                    message: 'Этот email уже используется.',
                    messageTemplate: 'Этот email уже используется.',
                    parameters: [],
                    root: $dto,
                    propertyPath: 'email',
                    invalidValue: $userName
                );
                $result->add($violation);
            } elseif ($this->repository->findOneByLogin($userName)) {
                $violation = new ConstraintViolation(
                    message: 'Пользователь с таким логином уже существует.',
                    messageTemplate: 'Пользователь с таким логином уже существует.',
                    parameters: [],
                    root: $dto,
                    propertyPath: 'username',
                    invalidValue: $userName
                );
                $result->add($violation);
            }
        }


        return $result;
    }

}
