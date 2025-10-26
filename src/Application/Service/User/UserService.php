<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Service\User;

use DateTime;
use Slcorp\RoleModelBundle\Domain\Entity\Role;
use Slcorp\RoleModelBundle\Application\DTO\UserCreateDTO;
use Slcorp\RoleModelBundle\Application\DTO\UserUpdateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\Mapper\UserMapper;
use Slcorp\RoleModelBundle\Application\Service\DTOMerger;
use Slcorp\RoleModelBundle\Domain\Entity\User;
use Slcorp\RoleModelBundle\Domain\Repository\EmailRepositoryInterface;
use Slcorp\RoleModelBundle\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserService
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private UserMapper $mapper,
        private DTOMerger $merger,
        private UserPasswordHasherInterface $passwordHasher,
        private EmailRepositoryInterface $repositoryEmail,
        private GidService $gidService,
    ) {
    }

    public function getUserById(int $id): ?User
    {
        return $this->repository->find($id);
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->repository->findByEmail($email);
    }

    public function create(UserCreateDTO $dto, bool $flush = true): User
    {
        $user = $this->mapper->fromCreateDto($dto);
        $userName = $dto->getEmail();
        $user->setUsername($userName);
        $plainPassword = $dto->getPlainPassword();
        if (null !== $plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->getPlainPassword());
            $user->setPassword($hashedPassword);
            $user->eraseCredentials();
        }
        if ($this->gidService->needGidGenerate()) {
            $gid = $this->gidService->generateGid(
                [
                    'lastname' => $dto->getLastname(),
                    'firstname' => $dto->getFirstname(),
                    'patronomic' => $dto->getPatronymic(),
                    "email" => $dto->getEmail(),
                ]
            );
            $user->setGid($gid);
        }

        return $this->repository->save($user, $flush);
    }

    public function update(UserUpdateDTO $user, User $targetUser, bool $flush = true): User
    {
        $userEntity = $this->mapper->fromUpdateDto($user);
        $this->merger->mergeDtoIntoEntity($targetUser, $userEntity);
        return $this->repository->save($targetUser, $flush);
    }

    public function delete(int $id, bool $flush = true): bool
    {
        if (!$user = $this->repository->find($id)) {
            throw BundleException::userNotFound($id);
        }
        $now = new DateTime();
        $user->setDelete(true);
        $user->setDeletedAt($now);
        $user->setUsername($user->getUsername() . '_' . $now->getTimestamp());
        $emails = $user->getEmails();
        foreach ($emails as $email) {
            $email->setEmail($email->getEmail() . '_' . $now->getTimestamp());
            $this->repositoryEmail->save($email, flush: false);
        }

        $this->repository->save($user, $flush);
        return true;
    }

    public function restore(int $id, bool $flush = true): bool
    {
        if (!$user = $this->repository->findDeleted($id)) {
            throw BundleException::userNotFound($id);
        }
        $user->setDelete(false);
        $user->setDeletedAt(null);
        $userNameParts = explode('_', $user->getUsername());
        if (count($userNameParts) > 1) {
            array_pop($userNameParts);
        }
        $user->setUsername(implode('_', $userNameParts));
        $emails = $user->getEmails();
        foreach ($emails as $email) {
            $emailValue = $email->getEmail();
            $emailParts = explode('_', $emailValue);
            if (count($emailParts) > 1) {
                array_pop($emailParts);
            }
            $email->setEmail(implode('_', $emailParts));
            $this->repositoryEmail->save($email, flush: false);
        }

        $this->repository->save($user, $flush);
        return true;
    }

    public function resetPassword(string $getPlainPassword, User $user, bool $flush): User
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $getPlainPassword);
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();
        return $this->repository->save($user, $flush);
    }

    public function findByRole(Role $role): array
    {
        return $this->repository->findByRole($role);
    }
}
