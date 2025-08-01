<?php
namespace App\ApiResource\Controller\Model;

use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\Model;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateModelController extends AbstractController {
    public function __construct(
        private StatusRepository $statusRepository,
        private EntityManagerInterface $em,
        private Security $security,
        private ValidatorInterface $validator, 
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

        $errors = $this->validator->validate($model);
        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $this->em->persist($model);
        $this->em->flush();

        return $model;
    }
}
