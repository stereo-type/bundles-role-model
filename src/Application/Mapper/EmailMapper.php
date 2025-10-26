<?php

/**
 * @package    UserMapper.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Mapper;

use Slcorp\RoleModelBundle\Application\DTO\EmailCreateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Domain\Entity\Email;
use Slcorp\RoleModelBundle\Domain\Repository\UserRepositoryInterface;

class EmailMapper
{
    public function fromDto(EmailCreateDTO $dto, UserRepositoryInterface $userRepository): Email
    {
        $user = $userRepository->find($dto->getUserId());
        if (!$user) {
            throw BundleException::userNotFound($dto->getUserId());
        }
        $email = new Email();
        $email->setEmail($dto->getEmail());
        $email->setUser($user);
        return $email;
    }
}
