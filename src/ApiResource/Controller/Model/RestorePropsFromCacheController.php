<?php

namespace App\ApiResource\Controller\Model;

use App\Repository\ModelRepository;
use App\Repository\StatusRepository;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class RestorePropsFromCacheController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private CacheItemPoolInterface $cachePool,
        private ModelRepository $modelRepository,
        private StatusRepository $statusRepository
    ) {}

    public function __invoke(int $id): JsonResponse
    {
        $model = $this->modelRepository->findOneBy(['id' => $id]);

        if (!$model) {
            throw new NotFoundHttpException('Model not found');
        }

        // Cherche dans le cache
        $cacheItem = $this->cachePool->getItem("model_{$id}_published");
        $cacheItemForStatus = $this->cachePool->getItem("model_{$id}_status");

        if (!$cacheItem->isHit() && !$cacheItemForStatus->isHit()) {
            return new JsonResponse(['error' => 'No cached data found for this model'], 404);
        }

        $cachedProps = $cacheItem->get();
        $cachedStatusId = $cacheItemForStatus->get();
        $cachedStatus = $this->statusRepository->findOneBy(['id' => $cachedStatusId]);

        // Met à jour props avec la valeur du cache
        $model->setProps($model->getPropsPublished());
        $model->setPropsPublished($cachedProps);
        if($cachedStatus) {
            $model->setStatus($cachedStatus);
        }
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Props restored from cache',
            'props' => $cachedProps,
        ]);
    }
}
