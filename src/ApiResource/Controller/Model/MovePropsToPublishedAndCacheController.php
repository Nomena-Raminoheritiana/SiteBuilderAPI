<?php
// src/ApiResource/Controller/Model/MovePropsToPublishedAndCacheController.php
namespace App\ApiResource\Controller\Model;

use App\Entity\Model;
use App\Repository\StatusRepository;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class MovePropsToPublishedAndCacheController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private CacheItemPoolInterface $cachePool,
        private StatusRepository $statusRepository
    ) {}

    public function __invoke(int $id): JsonResponse
    {
        $model = $this->em->getRepository(Model::class)->find($id);

        if (!$model) {
            throw new NotFoundHttpException('Model not found');
        }

         // Cache with key: model_{id}_published
        $cacheItem = $this->cachePool->getItem("model_{$id}_published");
        $cacheItem->set($model->getPropsPublished());
        $cacheItem->expiresAfter(3600); // 1h

        // Cache with key: model_{id}_status
        $cacheItemForStatus = $this->cachePool->getItem("model_{$id}_status");
        $cacheItemForStatus->set($model->getStatus()->getId());
        $cacheItemForStatus->expiresAfter(3600); // 1h

        $props = $model->getProps();
        $model->setPropsPublished($props);
        $model->setStatus($this->statusRepository->findOneBy(['code' => 'published']));
        $this->em->flush();

        $this->cachePool->save($cacheItem);
        $this->cachePool->save($cacheItemForStatus);

        return new JsonResponse([
            'message' => 'Props published and cached',
            'propsPublished' => $props
        ]);
    }
}
