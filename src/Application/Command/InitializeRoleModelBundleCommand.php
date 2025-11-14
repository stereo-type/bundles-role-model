<?php

/**
 * @package    InitializeRoleModelBundleCommand.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Application\Command;

use Slcorp\RoleModelBundle\Application\DTO\RoleCreateDTO;
use Slcorp\RoleModelBundle\Application\DTO\UserCreateDTO;
use Slcorp\RoleModelBundle\Application\Service\Role\RoleService;
use Slcorp\RoleModelBundle\Application\Service\User\UserService;
use Slcorp\RoleModelBundle\Application\UseCase\Role\RoleCreate;
use Slcorp\RoleModelBundle\Application\UseCase\User\UserCreate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(
    name: 'role_model_bundle:init',
    description: 'Команда инициализации ролевой системы'
)]
final class InitializeRoleModelBundleCommand extends Command
{
    use CommandTrait;

    public function __construct(
        private readonly RoleCreate $roleCreate,
        private readonly UserCreate $userCreate,
        private readonly RoleService $roleService,
        private readonly UserService $userService,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }


    protected function runCommand(): int
    {
        $roleAdmin = 'ROLE_ADMIN';
        $adminRole = $this->roleService->getRoleByName($roleAdmin);
        if (!$adminRole) {
            $adminRoleDto = new RoleCreateDTO();
            $adminRoleDto->setName($roleAdmin);
            $adminRoleDto->setDescription('Admin role');
            $adminRole = $this->roleCreate->execute($adminRoleDto);
        }

        $adminLogin = $_ENV['API_PLATFORM_SYSTEM_USER'] ?? trim($this->io->ask('Введите логин'));
        $adminPass = $_ENV['API_PLATFORM_SYSTEM_PASS'] ?? trim($this->io->ask('Введите пароль'));


        if (!$adminLogin && !$adminPass) {
            return Command::INVALID;
        }

        if (!$admin = $this->userService->getUserByEmail($adminLogin)) {
            $adminDto = new UserCreateDTO();
            $adminDto->setEmail($adminLogin);
            $adminDto->setPlainPassword($adminPass);
            $adminDto->setLastname('Admin');
            $adminDto->setFirstname('System');
            $admin = $this->userCreate->execute($adminDto);
        }


        $admin->addRole($adminRole);
        $this->entityManager->flush();
        return Command::SUCCESS;
    }

}
