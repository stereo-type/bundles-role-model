<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\DTO;

use Slcorp\CoreBundle\Application\DTO\DTOTrait;
use Slcorp\CoreBundle\Infrastructure\Validator\Constraints as CustomAssert;

class UserLoginDTO
{
    use DTOTrait;
    #[CustomAssert\NotBlank]
    #[CustomAssert\Length(min: 3, max: 255)]
    private string $username;

    #[CustomAssert\NotBlank]
    #[CustomAssert\Length(min: 3, max: 255)]
    private string $password;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): UserLoginDTO
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): UserLoginDTO
    {
        $this->password = $password;
        return $this;
    }




}
