<?php

namespace App\Security;

use App\Entity\User;
use App\Services\Token\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class LoginAuthenticator extends AbstractAuthenticator
{

    public function __construct(
        private JWTService $JWTService,
        private EntityManagerInterface $entityManager
    ) {

    }

    public function supports(Request $request): ?bool
    {
        return (
            $request->request->has('username') ||
            $request->request->has('password')
        );
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        if($user instanceof User) {
            $userToken = $this->JWTService->generateToken($user->getUserIdentifier());
            $user->setToken($userToken->toString());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return new JsonResponse([
                "token" => $userToken
            ], Response::HTTP_OK);
        }
        return new JsonResponse([
            "error" => "user not found"
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            "error" => "User not found"
        ], Response::HTTP_UNAUTHORIZED);
    }
}