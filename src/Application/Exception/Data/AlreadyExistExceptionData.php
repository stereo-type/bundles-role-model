<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Exception\Data;

use Slcorp\RoleModelBundle\Application\Exception\ExceptionType;
use Symfony\Component\HttpFoundation\Response;

class AlreadyExistExceptionData extends BundleExceptionData
{
    public function __construct(
        private readonly string $entity,
        private readonly string|int $id,
        ?string $message = null,
        ?string $title = null,
        private readonly ?string $field = 'id',
    ) {
        $this->message = $message ?? sprintf('Объект "%d" с ID = %d уже существует', $this->entity, (string)$this->id);
        $this->title = $title ?? sprintf('Объект "%d"  уже существует', $this->entity);
        parent::__construct(Response::HTTP_BAD_REQUEST, ExceptionType::AlreadyExist, $this->getMessage(), $this->getTitle());
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
