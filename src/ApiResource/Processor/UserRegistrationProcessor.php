<?php

namespace App\ApiResource\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Doctrine\Orm\State\PersistProcessor;
use App\Entity\User;
use App\Services\Token\JWTService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\State\ProcessorInterface;

class UserRegistrationProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTService $jwtService
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        if (!$data instanceof User) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPassword());
        $userToken = $this->jwtService->generateToken($data->getUserIdentifier());
        $data->setPassword($hashedPassword);
        $data->setToken($userToken->toString());
        $data->setRoles(['ROLE_ADMIN']);
        
        // Sauvegarder l'utilisateur
        $user = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        // Retourner l'utilisateur + token
        return $user;
    }
}
