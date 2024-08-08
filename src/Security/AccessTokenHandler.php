<?php

namespace App\Security;

use App\Repository\UserRepository;
use App\Services\Token\JWTService;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private JWTService $JWTService
    ) {
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $token = $this->JWTService->parseToken($accessToken);
        if(!$this->JWTService->verifyToken($token)) {
            throw new BadCredentialsException('Invalid credentials.');
        }
        $user = $this->userRepository->findOneBy([
            "token" => $accessToken
        ]);
        if($user === null) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        return new UserBadge($user->getUserIdentifier());
    }
}