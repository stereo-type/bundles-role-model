<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Service\Email;

use Slcorp\RoleModelBundle\Application\DTO\EmailCreateDTO;
use Slcorp\RoleModelBundle\Application\Exception\BundleException;
use Slcorp\RoleModelBundle\Application\Mapper\EmailMapper;
use Slcorp\RoleModelBundle\Application\Service\DTOMerger;
use Slcorp\RoleModelBundle\Domain\Entity\Email;
use Slcorp\RoleModelBundle\Domain\Entity\User;
use Slcorp\RoleModelBundle\Domain\Repository\EmailRepositoryInterface;
use Slcorp\RoleModelBundle\Domain\Repository\UserRepositoryInterface;

readonly class EmailService
{
    public function __construct(
        private EmailRepositoryInterface $repository,
        private UserRepositoryInterface $userRepository,
        private EmailMapper $mapper,
        private DTOMerger $DTOMerger,
    ) {
    }

    public function create(EmailCreateDTO $dto, bool $flush = true): Email
    {
        if ($this->repository->findByEmail($dto->getEmail())) {
            throw BundleException::emailAlreadyExist($dto->getEmail());
        }
        return $this->repository->save($this->mapper->fromDto($dto, $this->userRepository), $flush);
    }

    public function createForUser(string $email, User $user, bool $flush = true): Email
    {
        if ($this->repository->findByEmail($email)) {
            throw BundleException::emailAlreadyExist($email);
        }

        $instance = new Email();
        $instance->setEmail($email);
        $instance->setUser($user);

        return $this->repository->save($instance, $flush);
    }

    public function delete(int $id, bool $flush = true): bool
    {
        if (!$email = $this->repository->find($id)) {
            throw BundleException::emailNotFound($id);
        }
        $this->repository->delete($email, $flush);
        return true;
    }


    public function findEmailByEmail(string $email): ?Email
    {
        return $this->repository->findByEmail($email);
    }

    public function findEmail(int $id): ?Email
    {
        return $this->repository->find($id);
    }

    public function update(EmailCreateDTO $dto, Email $targetOperation, bool $flush): Email
    {
        $operationEntity = $this->mapper->fromDto($dto, $this->userRepository);
        $this->DTOMerger->mergeDtoIntoEntity($targetOperation, $operationEntity);
        return $this->repository->save($targetOperation, $flush);
    }
}
