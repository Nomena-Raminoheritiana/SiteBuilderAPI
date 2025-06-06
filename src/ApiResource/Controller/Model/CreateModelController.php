<?php
namespace App\ApiResource\Controller\Model;

use App\Entity\Model;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

class CreateModelController extends AbstractController {
     public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function __invoke(Model $model): Model
    {
        $user = $this->security->getUser();

        if ($user === null) {
            throw new \RuntimeException('No authenticated user found');
        }

        // Lier le modèle à l'utilisateur connecté
        $model->setUser($user);

        $this->em->persist($model);
        $this->em->flush();

        return $model;
    }
}