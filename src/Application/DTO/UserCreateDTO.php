<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\DTO;

use Slcorp\CoreBundle\Application\DTO\DTOTrait;
use Slcorp\RoleModelBundle\Domain\Entity\User;
use Slcorp\CoreBundle\Infrastructure\Validator\Constraints as CustomAssert;

class UserCreateDTO
{
    use DTOTrait;
    #[CustomAssert\NotBlank]
    #[CustomAssert\Email]
    #[CustomAssert\Length(min: 3, max: 255)]
    #[CustomAssert\UniqueValue(entityClass: User::class, field: 'username', message: 'Username "{{ value }}" уже используется')]
    private string $email;

    #[CustomAssert\Length(min: 3, max: 255)]
    private ?string $plainPassword = null;

    #[CustomAssert\NotBlank]
    #[CustomAssert\Length(min: 3, max: 255)]
    private string $firstname;

    #[CustomAssert\NotBlank]
    #[CustomAssert\Length(min: 3, max: 255)]
    private string $lastname;

    private ?string $patronymic = null;

    private ?string $avatar = null;

    #[CustomAssert\Length(exactly: 2)]
    private ?string $country = null;

    private ?string $region = null;

    private ?string $city = null;

    private ?string $address = null;

    #[CustomAssert\Length(min: 4, max: 100)]
    private ?string $adLogin = null;


    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): UserCreateDTO
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): UserCreateDTO
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): UserCreateDTO
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    public function setPatronymic(?string $patronymic): UserCreateDTO
    {
        $this->patronymic = $patronymic;
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): UserCreateDTO
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): UserCreateDTO
    {
        $this->country = $country;
        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): UserCreateDTO
    {
        $this->region = $region;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): UserCreateDTO
    {
        $this->city = $city;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): UserCreateDTO
    {
        $this->address = $address;
        return $this;
    }

    public function getAdLogin(): ?string
    {
        return $this->adLogin;
    }

    public function setAdLogin(?string $adLogin): UserCreateDTO
    {
        $this->adLogin = $adLogin;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): UserCreateDTO
    {
        $this->email = $email;
        return $this;
    }

}
