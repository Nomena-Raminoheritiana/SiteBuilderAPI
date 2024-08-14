<?php

namespace App\Security\AccessTokenAuthentication;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthenticationSuccessHandler implements \Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        if(str_contains($request->getUri(), "/api/verifyToken")) {
            return new JsonResponse([
                'verified' => true
            ]);
        }
       return null;
    }
}