<?php 
namespace App\ApiResource\Controller\Model;

use App\Repository\ModelRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class GetModelsByUserUuidController extends AbstractController {

    public function __invoke(
        Request $request,
        ModelRepository $modelRepository,
        UserRepository $userRepository
    ): array
    {
        try {
            $authHeader = $request->headers->get('Authorization');
            $user = $userRepository->findOneBy(['token'=> substr($authHeader, 7)]);
            if (!$user) {
                throw new NotFoundHttpException('User not found.');
            }
            return $modelRepository->findBy(['user' => $user]);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException('UUID not valid.');
        }
    }
}