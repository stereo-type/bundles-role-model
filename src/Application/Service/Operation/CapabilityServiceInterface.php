<?php

/**
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Service\Operation;

use Slcorp\RoleModelBundle\Domain\Entity\Operation;
use Symfony\Component\Security\Core\User\UserInterface;

interface CapabilityServiceInterface
{
    public function createOperation(string $code, string $name, ?int $parentId = null, ?string $comment = null, ?string $description = null): Operation;

    public function hasUserCapability(UserInterface $user, string $code): bool;

    public function hasCurrentUserCapability(string $code): bool;

    public function translate(string $string): string;

}
