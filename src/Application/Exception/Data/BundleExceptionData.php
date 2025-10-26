<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Exception\Data;

use Slcorp\RoleModelBundle\Application\Exception\ExceptionType;
use Symfony\Component\HttpFoundation\Response;

class BundleExceptionData
{
    protected string $title;
    protected string $message;

    public function __construct(
        protected int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        protected ExceptionType $type = ExceptionType::UnExpected,
        ?string $message = null,
        ?string $title = null,
    ) {
        $this->message = $message ?? 'Ошибка получения данных';
        $this->title = $title ?? 'Ошибка';
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getType(): ExceptionType
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): BundleExceptionData
    {
        $this->title = $title;
        return $this;
    }


    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
        ];
    }
}
