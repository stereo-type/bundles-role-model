<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Domain\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Slcorp\RoleModelBundle\Application\DTO\RefreshTokenDTO;
use Slcorp\RoleModelBundle\Application\DTO\RefreshTokenResponseDTO;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Slcorp\RoleModelBundle\Presentation\Controller\RefreshTokenController;
use Symfony\Component\Security\Core\User\UserInterface;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/role-model-bundle/users/refresh-token',
            controller:  RefreshTokenController::class,
            shortName:   'Login Check',
            input:       RefreshTokenDTO::class,
            output:      RefreshTokenResponseDTO::class
        ),
    ]
)]
#[ORM\Entity]
#[ORM\Table(name: 'role_model_bundle_refresh_tokens')]
class RefreshToken implements RefreshTokenInterface
{
    #[
        ORM\Id,
        ORM\Column(
            name: 'id',
            type: Types::INTEGER,
            nullable: false,
        ),
        ORM\GeneratedValue(strategy: 'AUTO'),
    ]
    /**@phpstan-ignore-next-line */
    protected int|string|null $id = null;

    #[ORM\Column(
        name: 'refresh_token',
        type: Types::STRING,
        length: 128,
        unique: true,
        nullable: false,
    )]
    protected ?string $refreshToken = null;

    #[ORM\Column(
        name: 'username',
        type: Types::STRING,
        nullable: false,
    )]
    protected ?string $username = null;

    #[ORM\Column(
        name: 'valid',
        type: Types::DATETIME_MUTABLE,
        nullable: false,
    )]
    protected ?DateTimeInterface $valid = null;


    /**
     * Creates a new model instance based on the provided details.
     */
    public static function createForUserWithTtl(string $refreshToken, UserInterface $user, int $ttl): static
    {
        $valid = new DateTime();

        // Explicitly check for a negative number based on a behavior change in PHP 8.2, see https://github.com/php/php-src/issues/9950
        if ($ttl > 0) {
            $valid->modify('+'.$ttl.' seconds');
        } elseif ($ttl < 0) {
            $valid->modify($ttl.' seconds');
        }

        $model = new static();
        $model->setRefreshToken($refreshToken);
        $model->setUsername(method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : $user->getUsername());
        $model->setValid($valid);

        return $model;
    }

    public function __toString(): string
    {
        return !in_array($this->getRefreshToken(), [null, '', '0'], true) ? $this->getRefreshToken() : '';
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function setRefreshToken(string $refreshToken): static
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setValid(DateTimeInterface $valid): static
    {
        $this->valid = $valid;

        return $this;
    }

    public function getValid(): ?DateTimeInterface
    {
        return $this->valid;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function isValid(): bool
    {
        return null !== $this->valid && $this->valid >= new DateTime();
    }

}
