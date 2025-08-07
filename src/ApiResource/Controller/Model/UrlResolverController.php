<?php

namespace App\ApiResource\Controller\Model;

use App\ApiResource\Dto\Input\Model\UrlResolverInput;
use App\Entity\Model;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UrlResolverController extends AbstractController 
{
    public function __invoke(UrlResolverInput $data, EntityManagerInterface $em): JsonResponse
    {
        $url = $data->url;
        $modelId = $data->modelId;

        // Chercher dans la base le Model avec cette URL
        $modelRepository = $em->getRepository(Model::class);
        $currentModel = $modelRepository->findOneBy(['id' => $modelId]);
        dump($currentModel,$url,$modelId);
        if (!$currentModel) {
            return new JsonResponse(['error' => 'URL not found'], 404);
        }
        $parent = $currentModel->getParent() ?? $currentModel;
        $model = $modelRepository->findOneBy(['parent'=> $parent, 'url' => $url]);
        dump($model, $parent);
        if (!$model) {
            return new JsonResponse(['error' => 'URL not found'], 404);
        }
       

        // Retourner les infos nécessaires (id, name etc)
        return new JsonResponse([
            'id' => $model->getId(),
            'url' => $model->getUrl(),
        ]);
    }
}
