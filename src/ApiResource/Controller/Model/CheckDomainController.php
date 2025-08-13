<?php 

namespace App\ApiResource\Controller\Model;

use App\ApiResource\Dto\Input\Model\CheckDomainInput;
use App\Entity\Model;
use App\Repository\ModelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CheckDomainController extends AbstractController
{
    public function __invoke(CheckDomainInput $data, ModelRepository $modelRepository): JsonResponse
    {
        $modelId = $data->modelId;
        $domain = $data->domain;

        if (!$domain) {
            return new JsonResponse(['error' => 'Domain is required'], 400);
        }

        /** @var Model|null $currentModel */
        $currentModel = $modelId ? $modelRepository->find($modelId) : null;

        // Récupère tous les models avec ce domaine
        $modelsWithDomain = $modelRepository->findBy(['domain' => $domain]);

        foreach ($modelsWithDomain as $model) {
            if ($currentModel && $model->getId() === $currentModel->getId()) {
                continue; // Same model
            }

            if ($currentModel && $currentModel->getChildren()->contains($model)) {
                continue; // model children
            }

            if ($currentModel && $model->getId() === $currentModel->getParent()?->getId()) {
                continue; //model parent
            }

            // Conflict
            return new JsonResponse(['conflictDomain' => true]);
        }

        return new JsonResponse(['conflictDomain' => false]);
    }
}