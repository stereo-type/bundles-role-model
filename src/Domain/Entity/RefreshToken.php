<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Domain\Entity;

use Slcorp\RoleModelBundle\Application\DTO\RefreshTokenDTO;
use Slcorp\RoleModelBundle\Application\DTO\RefreshTokenResponseDTO;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/role-model-bundle/users/refresh-token',
            controller: 'gesdinet.jwtrefreshtoken::refresh',
            shortName: 'Login Check',
            input: RefreshTokenDTO::class,
            output: RefreshTokenResponseDTO::class
        ),
    ]
)]
#[ORM\Entity]
#[ORM\Table(name: 'role_model_bundle_refresh_tokens')]
class RefreshToken extends BaseRefreshToken
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    #[ORM\Column(type: 'string', length: 128, unique: true)]
    protected $refreshToken;

    #[ORM\Column(type: 'string', length: 255)]
    protected $username;

    #[ORM\Column(type: 'datetime')]
    protected $valid;
}
