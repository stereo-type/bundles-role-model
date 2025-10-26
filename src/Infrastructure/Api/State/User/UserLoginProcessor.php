<?php

/**
 * @package    UserLoginProcessor.php
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Infrastructure\Api\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use InvalidArgumentException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Slcorp\RoleModelBundle\Application\DTO\UserLoginDTO;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @template UserLoginDTO
 * @template array
 * @implements ProcessorInterface<UserLoginDTO, array>
 */
class UserLoginProcessor implements ProcessorInterface
{
    public function __construct(private JWTTokenManagerInterface $jwtManager)
    {

    }


    /**
     * @param UserLoginDTO $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return array
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        // Предполагаем, что данные содержат информацию о пользователе
        if (!$data instanceof UserInterface) {
            throw new InvalidArgumentException('Должен быть передан UserInterface');
        }

        $token = $this->jwtManager->create($data);

        return [
            'token' => $token,
        ];
    }
}
