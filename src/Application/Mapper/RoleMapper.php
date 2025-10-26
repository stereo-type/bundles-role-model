<?php

/**
 * @package    UserMapper.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Mapper;

use Slcorp\RoleModelBundle\Application\DTO\RoleCreateDTO;
use Slcorp\RoleModelBundle\Domain\Entity\Role;

class RoleMapper
{
    use DtoToEntityTrait;
    public function fromDto(RoleCreateDTO $dto): Role
    {
        $role = new Role();
        $this->mapDtoToEntity($dto, $role);
        return $role;
    }
}
