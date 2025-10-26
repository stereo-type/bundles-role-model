<?php

/**
 * @package    RefreshTokenDTO.php
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiProperty;

class RefreshTokenDTO
{
    #[ApiProperty(description: "Токен обновления")]
    #[Assert\NotBlank]
    public string $refreshToken;
}
