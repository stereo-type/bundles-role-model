<?php

/**
 * @package    RefreshTokenController.php
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace Slcorp\RoleModelBundle\Presentation\Controller;

use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Request\Extractor\ExtractorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

#[Route('/role-model-bundle/operation')]
final class RefreshTokenController extends AbstractController
{
    public function __construct(
        private readonly RefreshTokenManagerInterface   $refreshTokenManager,
        private readonly JWTTokenManagerInterface       $jwtManager,
        private readonly UserProviderInterface          $userProvider,
        private readonly ExtractorInterface             $tokenExtractor,
        private readonly RefreshTokenGeneratorInterface $refreshTokenGenerator,
        private readonly ParameterBagInterface          $params,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $parameterName = $this->params->get('gesdinet_jwt_refresh_token.token_parameter_name');
        $token = $this->tokenExtractor->getRefreshToken($request, $parameterName);

        if (!$token) {
            throw new AuthenticationException('No refresh token provided.');
        }

        $refreshToken = $this->refreshTokenManager->get($token);
        if (!$refreshToken || !$refreshToken->isValid()) {
            throw new AuthenticationException('Invalid refresh token.');
        }

        $username = $refreshToken->getUsername();
        $user = $this->userProvider->loadUserByIdentifier($username);

        $jwt = $this->jwtManager->create($user);

        // опционально — пересоздать refresh токен (single-use mode)
        $newRefresh = $this->refreshTokenGenerator->createForUserWithTtl($user, 3600);
        $this->refreshTokenManager->save($newRefresh);

        return new JsonResponse(
            [
                'token'         => $jwt,
                'refresh_token' => $newRefresh->getRefreshToken(),
            ]
        );
    }
}
