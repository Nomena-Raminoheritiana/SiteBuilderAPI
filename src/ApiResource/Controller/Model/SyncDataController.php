<?php
namespace App\ApiResource\Controller\Model;

use App\ApiResource\Dto\Input\Model\ModelSyncInput;
use App\Repository\ModelRepository;
use App\Services\ArraySynchronizer;
use App\Services\DumpSqlService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SyncDataController extends AbstractController {
     public function __construct(
        private ModelRepository $modelRepository,
        private EntityManagerInterface $em,
        private DumpSqlService $dumpSqlService
    ) {}

    public function __invoke(ModelSyncInput $data): JsonResponse
    {
        try {
                $models = $this->modelRepository->findBySlugAndPropsNotNull($data->slug);
                if (empty($models)) {
                    throw new NotFoundHttpException("No model found for the slug : {$data->slug}");
                }

                $returnCode = $this->dumpSqlService->dumpSql();
                if($returnCode == Command::SUCCESS) {
                    foreach ($models as $model) {
                        $syncedProps = ArraySynchronizer::synchronize($data->defaultProps, $model->getProps() ?? []);
                        $model->setProps($syncedProps);
                        $this->em->persist($model);
                    }

                    $this->em->flush();
                    return new JsonResponse([
                        'status' => 'success',
                        'message' => count($models) . ' model successfuly synced',
                    ]);
                }
            
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Error while dumping the sql',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);

        }
        catch(NotFoundHttpException $e) {
             return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }
        catch (\Throwable $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Error while synchronising the models : ' . $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}