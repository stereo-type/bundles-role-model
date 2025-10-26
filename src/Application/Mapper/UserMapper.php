<?php

/**
 * @package    UserMapper.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Mapper;

use Slcorp\RoleModelBundle\Application\DTO\UserCreateDTO;
use Slcorp\RoleModelBundle\Application\DTO\UserUpdateDTO;
use Slcorp\RoleModelBundle\Domain\Entity\User;

class UserMapper
{
    use DtoToEntityTrait;
    public function fromCreateDto(UserCreateDTO $dto): User
    {
        $role = new User();
        $this->mapDtoToEntity($dto, $role);
        return $role;
    }
    public function fromUpdateDto(UserUpdateDTO $dto): User
    {
        $role = new User();
        $this->mapDtoToEntity($dto, $role);
        return $role;
    }
}
