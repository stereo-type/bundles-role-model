<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Domain\Entity;

use Slcorp\RoleModelBundle\Application\DTO\UserResetPasswordAdminDTO;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\User\UserResetPasswordAdminProcessor;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Slcorp\CoreBundle\Domain\Entity\Traits\DeletedEntityTrait;
use Slcorp\CoreBundle\Domain\Entity\Traits\HasTimestamps;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Slcorp\RoleModelBundle\Application\DTO\UserCreateDTO;
use Slcorp\RoleModelBundle\Application\DTO\UserLoginDTO;
use Slcorp\RoleModelBundle\Application\DTO\UserResetPasswordDTO;
use Slcorp\RoleModelBundle\Application\DTO\UserUpdateDTO;
use Slcorp\RoleModelBundle\Infrastructure\Api\Filter\User\FullNameSearchFilter;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\User\UserDeleteProcessor;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\User\UserLoginProcessor;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\User\UserCreateProcessor;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\User\UserResetPasswordProcessor;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\User\UserUpdateProcessor;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\User\CurrentUserGetProcessor;
use Slcorp\RoleModelBundle\Infrastructure\Api\State\User\UserRestoreProcessor;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: User::PATH),
        new Get(
            uriTemplate: User::PATH . '/me',
            security: 'is_granted("ROLE_USER")',
            provider: CurrentUserGetProcessor::class,
            processor: CurrentUserGetProcessor::class
        ),
        new Post(
            uriTemplate: '/role-model-bundle/users/registration',
            normalizationContext: ['groups' => ['user:registration']],
            input: UserCreateDTO::class,
            processor: UserCreateProcessor::class,
        ),
        new Post(
            uriTemplate: '/role-model-bundle/users/login',
            input: UserLoginDTO::class,
            processor: UserLoginProcessor::class,
        ),
        new Post(
            uriTemplate: '/role-model-bundle/users/{id}/reset-password',
            normalizationContext: ['groups' => ['user:reset-password']],
            security: 'object == user',
            securityMessage: 'Обновлять пароль можно только себе',
            input: UserResetPasswordDTO::class,
            processor: UserResetPasswordProcessor::class,
        ),
        new Get(uriTemplate: User::PATH_ID),
        new Patch(
            uriTemplate: User::PATH_ID,
            security: 'object == user or is_granted("ROLE_ADMIN")',
            securityMessage: 'Обновлять данные можно только себе',
            input: UserUpdateDTO::class,
            processor: UserUpdateProcessor::class,
        ),
        new Delete(
            uriTemplate: User::PATH_ID,
            security: 'object == user or is_granted("ROLE_ADMIN")',
            securityMessage: 'Удалять можно только себя',
            processor: UserDeleteProcessor::class,
        ),
        new Post(
            uriTemplate: '/role-model-bundle/users/{id}/restore-user',
            normalizationContext: ['groups' => ['user:restore']],
            denormalizationContext: ['groups' => ['user:restore']],
            security: 'is_granted("ROLE_ADMIN")',
            processor: UserRestoreProcessor::class,
        ),
        new Post(
            uriTemplate: '/role-model-bundle/users/{id}/reset-password-admin',
            normalizationContext: ['groups' => ['user:reset-password']],
            //            security: 'is_granted("ROLE_ADMIN")',
            securityMessage: 'Обновлять пароль данным методом может только администратор',
            input: UserResetPasswordAdminDTO::class,
            processor: UserResetPasswordAdminProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => ['user:read']],
)]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'username' => 'partial'])]
#[ApiFilter(FullNameSearchFilter::class)]
#[ORM\Entity]
#[ORM\Table(name: 'role_model_bundle_users')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use DeletedEntityTrait;
    use HasTimestamps;

    public const PATH_ID = '/role-model-bundle/users/{id}';
    public const PATH = '/role-model-bundle/users';

    #[Groups(['user:read', 'user:restore', 'user:reset-password', 'user:registration'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    //    #[Groups(['user:read'])]
    #[ORM\Column(length: 255, unique: true)]
    private string $username;

    #[ORM\Column(options: ['default' => true])]
    private bool $active = true;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    #[SerializedName('password')]
    private ?string $plainPassword;

    #[Groups(['user:read'])]
    #[ORM\Column(length: 255)]
    private string $firstname;

    #[Groups(['user:read'])]
    #[ORM\Column(length: 255)]
    private string $lastname;

    #[Groups(['user:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $patronymic = null;

    #[Groups(['user:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[Groups(['user:read'])]
    #[ORM\Column(length: 2, nullable: true)]
    private ?string $country = null;

    #[Groups(['user:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $region = null;

    #[Groups(['user:read'])]
    #[ORM\Column(length: 120, nullable: true)]
    private ?string $city = null;

    #[Groups(['user:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[Groups(['user:read'])]
    #[ORM\Column(length: 100, unique: true, nullable: true)]
    private ?string $ad_login = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $deleted_at = null;

    /** @var Collection<int, Role> */
    #[Groups(['user:read'])]
    #[ORM\ManyToMany(targetEntity: Role::class, cascade: ['persist'])]
    #[ORM\JoinTable(
        name: 'role_model_bundle_user_roles',
        joinColumns: [new ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id')]
    )]
    private Collection $userRoles;

    /** @var Collection<int, Email> */
    #[ORM\OneToMany(targetEntity: Email::class, mappedBy: 'user', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $emails;


    #[Groups(['user:read', 'user:restore', 'user:reset-password', 'user:registration'])]
    #[ORM\Column(type: 'string', length: 256, nullable: true)]
    private ?string $gid = null;


    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->emails = new ArrayCollection();
    }


    public function setId(?int $id): User
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): User
    {
        $this->active = $active;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): User
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): User
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): User
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    public function setPatronymic(?string $patronymic): User
    {
        $this->patronymic = $patronymic;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): User
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): User
    {
        $this->country = $country;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): User
    {
        $this->region = $region;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): User
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): User
    {
        $this->address = $address;

        return $this;
    }

    public function getAdLogin(): ?string
    {
        return $this->ad_login;
    }

    public function setAdLogin(?string $ad_login): User
    {
        $this->ad_login = $ad_login;

        return $this;
    }

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?DateTimeInterface $deleted_at): User
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    /**
     * @param Collection<int, Role> $userRoles
     * @return User
     */
    public function setUserRoles(Collection $userRoles): User
    {
        $this->userRoles = $userRoles;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->userRoles->map(fn (Role $role) => $role->getName())->toArray();
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function addRole(Role $role): User
    {
        foreach ($this->userRoles as $r) {
            if ($r->getId() === $role->getId()) {
                return $this;
            }
        }

        $this->userRoles->add($role);
        return $this;
    }

    public function removeRole(Role $role): User
    {
        foreach ($this->userRoles as $r) {
            if ($r->getId() === $role->getId()) {
                $this->userRoles->removeElement($r);
                return $this;
            }
        }
        return $this;
    }

    public function getGid(): ?string
    {
        return $this->gid;
    }

    public function setGid(?string $gid): User
    {
        $this->gid = $gid;
        return $this;
    }

    /**
     * @return Collection<int, Email>
     */
    public function getEmails(): Collection
    {
        return $this->emails;
    }

    public function addEmail(Email $email): static
    {
        foreach ($this->emails as $e) {
            if ($e->getId() === $email->getId()) {
                return $this;
            }
        }

        $this->emails->add($email);
        $email->setUser($this);

        return $this;
    }

    public function removeEmail(Email $email): static
    {
        foreach ($this->emails as $e) {
            if ($e->getId() === $email->getId()) {
                $this->emails->removeElement($email);
                return $this;
            }
        }

        return $this;
    }

    public function getFullName(): string
    {
        return $this->lastname . ' ' . $this->firstname . ($this->patronymic ? ' ' . $this->patronymic : '');
    }

    public function getInitials(): string
    {
        $firstname = strtoupper($this->firstname[0]);
        $patronymicInitial = $this->patronymic ? strtoupper($this->patronymic[0]) : '';

        return $firstname . '.' . $patronymicInitial . '.';
    }

    /**
     * Проверяет, имеет ли пользователь доступ к операции.
     */
    public function hasCapability(?Operation $operation): bool
    {
        if (in_array('ROLE_ADMIN', $this->getRoles(), true)) {
            return true;
        }

        if (is_null($operation)) {
            return false;
        }
        $userOperationCodes = [];

        foreach ($this->userRoles as $role) {
            foreach ($role->getOperations() as $roleOperation) {
                $userOperationCodes[] = $roleOperation->getCode();
            }
        }
        $userOperationCodes = array_unique($userOperationCodes);

        return $this->isOperationAllowed($operation, $userOperationCodes);
    }

    /**
     * Рекурсивно проверяет наличие доступа по коду операции и её родителям.
     */
    private function isOperationAllowed(Operation $operation, array $allowedCodes): bool
    {
        if (in_array($operation->getCode(), $allowedCodes, true)) {
            return true;
        }
        $parentOperation = $operation->getParent();
        return $parentOperation && $this->isOperationAllowed($parentOperation, $allowedCodes);
    }
}
