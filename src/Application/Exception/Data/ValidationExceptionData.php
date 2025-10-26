<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Exception\Data;

use Slcorp\RoleModelBundle\Application\Exception\ExceptionType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationExceptionData extends BundleExceptionData
{
    public function __construct(
        private readonly ConstraintViolationListInterface $violations,
        int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY,
        ?string $message = null,
        ?string $title = null,
    ) {
        $this->message = $message ?? 'Ошибка валидации объекта';
        $this->title = $title ?? 'Ошибка';
        parent::__construct($statusCode, ExceptionType::ConstraintViolationList, $this->getMessage(), $this->getTitle());
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'violations' => $this->getViolationsArray(),
        ];
    }

    private function getViolationsArray(): array
    {
        $violations = [];
        foreach ($this->getViolations() as $violation) {
            $violations[] = [
                'propertyPath' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return $violations;
    }

    public function getMessage(): string
    {
        if ($_ENV['APP_DEBUG'] && ($_ENV['APP_ENV'] === 'dev' || $_ENV['APP_ENV'] === 'test')) {
            return $this->message .
                " :\n\r " . implode("\n\r ", array_map(static fn ($i) => $i['message'] . '-' . $i['propertyPath'], $this->getViolationsArray()));
        }
        return $this->message;
    }
}
