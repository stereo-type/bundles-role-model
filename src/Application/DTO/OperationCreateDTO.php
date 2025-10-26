<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\DTO;

use Slcorp\CoreBundle\Application\DTO\DTOTrait;
use Slcorp\RoleModelBundle\Domain\Entity\Operation;
use Slcorp\CoreBundle\Infrastructure\Validator\Constraints as CustomAssert;

class OperationCreateDTO
{
    use DTOTrait;

    #[CustomAssert\NotBlank]
    #[CustomAssert\Length(min: 3, max: 255)]
    #[CustomAssert\UniqueValue(entityClass: Operation::class, field: 'code', message: 'Код операции "{{ value }}" уже используется')]
    public string $code;

    #[CustomAssert\NotBlank]
    #[CustomAssert\Length(min: 3, max: 255)]
    public string $name;
    #[CustomAssert\NotBlank]
    #[CustomAssert\Length(min: 3, max: 255)]
    public string $comment;

    public ?int $rootId = null;

    public ?int $parentId = null;


    public ?string $description = null;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): OperationCreateDTO
    {
        $this->code = $code;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): OperationCreateDTO
    {
        $this->name = $name;
        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): OperationCreateDTO
    {
        $this->comment = $comment;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): OperationCreateDTO
    {
        $this->description = $description;
        return $this;
    }

    public function getRootId(): ?int
    {
        return $this->rootId;
    }

    public function setRootId(?int $rootId): OperationCreateDTO
    {
        $this->rootId = $rootId;
        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): OperationCreateDTO
    {
        $this->parentId = $parentId;
        return $this;
    }


}
