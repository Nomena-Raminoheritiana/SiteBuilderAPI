<?php

namespace App\ApiResource\Controller\Model;

use App\ApiResource\Dto\Input\Model\DuplicationInput;
use App\Entity\Model;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DuplicateModelController extends AbstractController 
{
    public function __invoke(DuplicationInput $data, EntityManagerInterface $em): Model
    {
        $modelId = $data->modelId;

        // Chercher le Model
        $modelRepository = $em->getRepository(Model::class);
        $currentModel = $modelRepository->findOneBy(['id' => $modelId]);
       
        if (!$currentModel) {
            throw new NotFoundHttpException('Model not found');
        }

        $newModel = clone $currentModel;
        $uuid = Uuid::uuid4()->toString();
        $newModel->setUrl('/'.$uuid);

        $em->persist($newModel);
        $em->flush();

        return $newModel; // API Platform va sérialiser l’entité
    }
}
