<?php 

namespace App\ApiResource\Controller\Model;

use App\ApiResource\Dto\Input\Model\CheckDomainInput;
use App\Entity\Model;
use App\Repository\ModelRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class GetChatBotConfigByModelIdController extends AbstractController
{
    public function __construct(
        private ModelRepository $model_repository,
        private UserRepository $userRepository,
       
    ) {}
    public function __invoke(string $id, Request $request): Model | JsonResponse
    {
        try {
            $authHeader = $request->headers->get('Authorization');
            $user = $this->userRepository->findOneBy(['token'=> substr($authHeader, 7)]);
            if (!$user) {
                throw new NotFoundHttpException('User not found.');
            }

            $model = $this->model_repository->findOneBy([
                "id" => $id,
                "user" => $user
            ]);
            if($model) {
                return  $model->getParent() ?? $model;
            }

           throw new NotFoundHttpException('Model not found.');

        } catch(\Exception $e) {
            throw $e;
        }
    }
}