<?php
namespace App\ApiResource\Controller\Model;

use App\Entity\Model;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

class CreateModelController extends AbstractController {
     public function __construct(
        private StatusRepository $statusRepository,
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function __invoke(Model $model): Model
    {
        $user = $this->security->getUser();
        $status = $this->statusRepository->findOneBy(['code' => 'draft']);

        if ($user === null) {
            throw new \RuntimeException('No authenticated user found');
        }

        // Lier le modèle à l'utilisateur connecté
        $model->setUser($user);
        $model->setStatus($status);

        $this->em->persist($model);
        $this->em->flush();

        return $model;
    }
}