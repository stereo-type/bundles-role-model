<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\DTO;

use Slcorp\CoreBundle\Application\DTO\DTOTrait;
use Slcorp\CoreBundle\Infrastructure\Validator\Constraints as CustomAssert;

class RoleCreateDTO
{
    use DTOTrait;
    #[CustomAssert\NotBlank]
    #[CustomAssert\Length(min: 3, max: 255)]
    private string $name;

    #[CustomAssert\Length(max: 255)]
    private ?string $description = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): RoleCreateDTO
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): RoleCreateDTO
    {
        $this->description = $description;
        return $this;
    }


}
