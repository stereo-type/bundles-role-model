<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Exception\Data;

use Slcorp\RoleModelBundle\Application\Exception\ExceptionType;
use Symfony\Component\HttpFoundation\Response;

class EntityNotFoundExceptionData extends BundleExceptionData
{
    public function __construct(
        private readonly string $entity,
        private readonly string|int $id,
        ?string $message = null,
        ?string $title = null,
        private readonly ?string $field = 'id',
    ) {
        $this->message = $message ?? sprintf('Объект "%s" с ID = %d не найден', $this->entity, (string)$this->id);
        $this->title = $title ?? sprintf('Объект "%s" не найден', $this->entity);
        parent::__construct(Response::HTTP_NOT_FOUND, ExceptionType::NotFound, $this->getMessage(), $this->getTitle());
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'entity' => $this->entity,
            'field' => $this->field,
            'fieldValue' => (string)$this->id,
        ];
    }
}
