<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\Token\JWTService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_security_login')]
    public function login(AuthenticationUtils $authenticationUtils): JsonResponse
    {
        $user = $this->getUser();
        if ($user instanceof User) {
            return $this->json(['token' => $user->getToken()]);
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->json(['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/api/verifyToken', name: 'app_security_verifytoken', methods: ['POST'])]
    public function verifyToken(Request $request, JWTService $JWTService): JsonResponse
    {
        if($request->getPayload()->has('token')) {
            $token = $JWTService->parseToken($request->getPayload()->get('token'));
            if($JWTService->verifyToken($token)) {
                return $this->json([
                    'verified' => true
                ]);
            }
        }
        return $this->json([
           'verified' => false
        ], Response::HTTP_UNAUTHORIZED);
    }
}
