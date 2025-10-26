<?php

/**
 * @package    RefreshTokenDTO.php
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\DTO;

class RefreshTokenResponseDTO
{
    public string $token;
    public string $refreshToken;
    public string $refreshTokenExpiration;
}
