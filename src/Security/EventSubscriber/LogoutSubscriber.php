<?php

// src/EventListener/LogoutSubscriber.php
namespace App\Security\EventSubscriber;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [LogoutEvent::class => 'onLogout'];
    }

    public function onLogout(LogoutEvent $event): void
    {
        // get the security token of the session that is about to be logged out
        $request = $event->getRequest();
        $authorizationHeader = $request->headers->get('Authorization');
        if ($authorizationHeader && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            $bearerToken = $matches[1];
            $user = $this->userRepository->findOneBy(['token' => $bearerToken]);
            if($user) {
                $user->setToken(null);
                $this->entityManager->flush();
            }
        }
        $response = New Response(null, Response::HTTP_NO_CONTENT);
        $event->setResponse($response);
    }
}