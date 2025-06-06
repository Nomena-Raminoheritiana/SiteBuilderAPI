<?php 
namespace App\ApiResource\Controller;

use App\Repository\ModelRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

#[AsController]
class GetModelByUserUuidController extends AbstractController {

    public function __invoke(
        string $uuid,
        ModelRepository $modelRepository,
        UserRepository $userRepository
    ): array
    {
        try {
            $user = $userRepository->findOneBy(['uuid'=> Uuid::fromString($uuid)]);
            if (!$user) {
                throw new NotFoundHttpException('User not found.');
            }
            return $modelRepository->findBy(['user' => $user]);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException('UUID not valid.');
        }
    }
}