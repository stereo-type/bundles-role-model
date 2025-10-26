<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Exception;

use Slcorp\RoleModelBundle\Application\Exception\Data\AlreadyExistExceptionData;
use Slcorp\RoleModelBundle\Application\Exception\Data\BundleExceptionData;
use Slcorp\RoleModelBundle\Application\Exception\Data\EntityNotFoundExceptionData;
use Slcorp\RoleModelBundle\Application\Exception\Data\ValidationExceptionData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class BundleException extends HttpException
{
    public function __construct(protected BundleExceptionData $exceptionData)
    {
        parent::__construct($exceptionData->getStatusCode(), $exceptionData->getMessage());
    }

    public function getExceptionData(): BundleExceptionData
    {
        return $this->exceptionData;
    }

    public static function userNotFound(int $id): self
    {
        return new self(
            new EntityNotFoundExceptionData(
                'User',
                $id,
                sprintf('Пользователь с ID = %d не найден', $id),
                'Пользователь не найден'
            )
        );
    }

    public static function userNotFoundLogin(string $login): self
    {
        return new self(
            new EntityNotFoundExceptionData(
                'User',
                $login,
                sprintf('Пользователь "%s" не найден', $login),
                'Пользователь не найден',
                field: 'login'
            )
        );
    }

    public static function userAlreadyExist(string $login): self
    {
        return new self(
            new AlreadyExistExceptionData(
                'User',
                $login,
                sprintf('Пользователь "%s" уже существует', $login),
                'Пользователь уже существует',
                field: 'username'
            )
        );
    }

    public static function incorrectAuthData(string $login): self
    {
        return new self(
            new BundleExceptionData(
                Response::HTTP_BAD_REQUEST,
                ExceptionType::IncorrectData,
                sprintf('Не верные данные авторизации для пользователя "%s"', $login),
                'Ошибка авторизации'
            )
        );
    }

    public static function roleNotFound(int $id): self
    {
        return new self(
            new EntityNotFoundExceptionData(
                'Role',
                $id,
                sprintf('Роль с ID = %s не найдена', $id),
                'Роль не найдена'
            )
        );
    }

    public static function roleAlreadyExist(int $id): self
    {
        return new self(
            new AlreadyExistExceptionData(
                'Role',
                $id,
                sprintf('Роль с ID = "%s" уже существует', $id),
                'Роль уже существует'
            )
        );
    }

    public static function roleNotFoundName(string $name): self
    {
        return new self(
            new EntityNotFoundExceptionData(
                'Role',
                $name,
                sprintf('Роль "%s" не найдена', $name),
                'Роль не найдена',
                field: 'name'
            )
        );
    }

    public static function roleAlreadyExistName(string $name): self
    {
        return new self(
            new AlreadyExistExceptionData(
                'Role',
                $name,
                sprintf('Роль "%s" уже существует', $name),
                'Роль уже существует',
                field: 'name'
            )
        );
    }

    public static function validationErrors(ConstraintViolationListInterface $violations): self
    {
        return new self(new ValidationExceptionData($violations));
    }

    public static function operationNotFound(int $id): self
    {
        return new self(
            new EntityNotFoundExceptionData(
                'Operation',
                $id,
                sprintf('Операция с ID = %s не найдена', $id),
                'Операция не найдена'
            )
        );
    }

    public static function operationNotFoundCode(string $name): self
    {
        return new self(
            new EntityNotFoundExceptionData(
                'Operation',
                $name,
                sprintf('Операция "%s" не найдена', $name),
                'Операция не найдена',
                field: 'code'
            )
        );
    }

    public static function operationAlreadyExistCode(string $code): self
    {
        return new self(
            new AlreadyExistExceptionData(
                'Operation',
                $code,
                sprintf('Операция "%s" уже существует', $code),
                'Операция уже существует',
                field: 'code'
            )
        );
    }

    public static function emailAlreadyExist(string $email): self
    {
        return new self(
            new AlreadyExistExceptionData(
                'Email',
                $email,
                sprintf('Email "%s" уже существует', $email),
                'Email уже существует',
                field: 'email'
            )
        );
    }

    public static function emailNotFoundEmail(string $email): self
    {
        return new self(
            new EntityNotFoundExceptionData(
                'Email',
                $email,
                sprintf('Email "%s" не найден', $email),
                'Email не найден',
                field: 'email'
            )
        );
    }

    public static function emailNotFound(int $id): self
    {
        return new self(
            new EntityNotFoundExceptionData(
                'Email',
                $id,
                sprintf('Email с ID = %s не найден', $id),
                'Email не найден'
            )
        );
    }

    public static function customMessage(string $message, ?string $title = 'Ошибка', int $code = Response::HTTP_BAD_REQUEST): self
    {
        return new self(
            new BundleExceptionData(
                statusCode: $code,
                message: $message,
                title: $title,
            )
        );
    }
}
