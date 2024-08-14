<?php

namespace App\Security\AccessTokenAuthentication;

use App\Repository\UserRepository;
use App\Services\Token\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private JWTService $JWTService,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $token = $this->JWTService->parseToken($accessToken);
        $user = $this->userRepository->findOneBy([
            "token" => $accessToken
        ]);
        if(!$this->JWTService->verifyToken($token)) {
            if($user !== null) {
                $user->setToken(null);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
            throw new BadCredentialsException('Invalid credentials.');
        }
        $user === null && throw new BadCredentialsException('Invalid credentials.');

        return new UserBadge($user->getUserIdentifier());
    }
}