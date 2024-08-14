<?php

namespace App\Security\AccessTokenAuthentication;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthenticationFailureHandler implements \Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new JsonResponse([
            'verified' => false
        ],Response::HTTP_UNAUTHORIZED);
    }
}