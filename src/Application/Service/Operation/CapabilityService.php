<?php

/**
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Service\Operation;

use Slcorp\CoreBundle\Application\Enum\SessionCacheKeys;
use Slcorp\CoreBundle\Application\Service\SessionCacheService;
use Slcorp\RoleModelBundle\Application\DTO\OperationCreateDTO;
use Slcorp\RoleModelBundle\Application\UseCase\Operation\OperationCreate;
use Slcorp\RoleModelBundle\Domain\Entity\Operation;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class CapabilityService implements CapabilityServiceInterface
{
    public function __construct(
        protected TranslatorInterface $translator,
        protected OperationService $operationService,
        protected OperationCreate $operationCreate,
        protected Security $security,
        protected SessionCacheService $sessionCache,
    ) {
    }

    public function translate(string $string): string
    {
        $lower = strtolower($string);
        $translate = $this->translator->trans($lower);
        if ($translate === $lower) {
            return $string;
        }

        return $translate;
    }

    public function createOperation(string $code, string $name, ?int $parentId = null, ?string $comment = null, ?string $description = null): Operation
    {
        $importGroup = new OperationCreateDTO();
        if (!$operation = $this->operationService->findByCode($code)) {
            $importGroup->setCode($code);
            $importGroup->setName($name);
            $importGroup->setComment($comment ?? $name);
            $importGroup->setDescription($description ?? $name);
            $importGroup->setParentId($parentId);

            return $this->operationCreate->execute($importGroup);
        }

        return $operation;
    }

    public function hasUserCapability(UserInterface $user, string $code): bool
    {
        /**У админа все права*/
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        /**Ошибка операции не существует, значит есть права*/
        if (!$this->operationService->findByCode($code)) {
            return true;
        }

        $userCapabilities = $this->userOperationsList($user);

        return in_array($code, $userCapabilities, true);
    }

    public function hasCurrentUserCapability(string $code): bool
    {
        $user = $this->security->getUser();
        if (null === $user) {
            return false;
        }

        return $this->hasUserCapability($user, $code);
    }


    /**Получения списка Capabilities пользователя с кешированием по ключу сессии
     * @param UserInterface|null $user
     * @return array
     * @throws InvalidArgumentException
     */
    public function userOperationsList(?UserInterface $user = null): array
    {
        $user = $user ?? $this->security->getUser();
        if (null !== $user) {
            $userCapabilities = $this->sessionCache->getData(
                SessionCacheKeys::USER_OPERATIONS_LIST,
                fn () => $this->operationService->flatOperationsList($this->security->getUser())
            );
        } else {
            $userCapabilities = [];
        }

        return $userCapabilities;
    }
}
