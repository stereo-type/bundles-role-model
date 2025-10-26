<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\DTO;

use Slcorp\CoreBundle\Application\DTO\DTOTrait;
use Slcorp\RoleModelBundle\Domain\Entity\Email;
use Slcorp\CoreBundle\Infrastructure\Validator\Constraints as CustomAssert;

class EmailCreateDTO
{
    use DTOTrait;

    #[CustomAssert\NotBlank]
    #[CustomAssert\Email]
    #[CustomAssert\UniqueValue(entityClass: Email::class, field: 'email', message: 'Email "{{ value }}" уже используется')]
    private string $email;
    #[CustomAssert\NotBlank]
    private int $userId;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): EmailCreateDTO
    {
        $this->email = $email;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): EmailCreateDTO
    {
        $this->userId = $userId;
        return $this;
    }




}
