<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\DTO;

use Slcorp\CoreBundle\Application\DTO\DTOTrait;
use Slcorp\CoreBundle\Infrastructure\Validator\Constraints as CustomAssert;

class UserResetPasswordDTO
{
    use DTOTrait;
    #[CustomAssert\NotBlank]
    #[CustomAssert\Length(min: 3, max: 255)]
    private string $oldPassword;
    #[CustomAssert\NotBlank]
    #[CustomAssert\Length(min: 3, max: 255)]
    private string $plainPassword;

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): UserResetPasswordDTO
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): UserResetPasswordDTO
    {
        $this->oldPassword = $oldPassword;
        return $this;
    }


}
