<?php 
namespace App\ApiResource\Controller\Model;

use App\Entity\Model;
use App\Repository\ModelRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        UserRepository $userRepository,
        EntityManagerInterface $em
    ): array
    {
        try {
            $authHeader = $request->headers->get('Authorization');
            $user = $userRepository->findOneBy(['token'=> substr($authHeader, 7)]);
            if (!$user) {
                throw new NotFoundHttpException('User not found.');
            }
            // Initialiser avec filtre obligatoire : user
            $criteria = ['user' => $user];
            $queryParams = $request->query->all();
            $meta = $em->getClassMetadata(Model::class);

            foreach ($queryParams as $field => $value) {
                if ($value === 'null') {
                    $criteria[$field] = null;
                    continue;
                }

                if ($meta->hasAssociation($field)) {
                    $targetClass = $meta->getAssociationTargetClass($field);
                    $repo = $em->getRepository($targetClass);
                    $relatedEntity = $repo->find($value);

                    if (!$relatedEntity) {
                        throw new BadRequestHttpException("Invalid ID for '$field': $value");
                    }

                    $criteria[$field] = $relatedEntity;
                } elseif ($meta->hasField($field)) {
                    $criteria[$field] = $value;
                }
            }

            return $modelRepository->findBy($criteria);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException('UUID not valid.');
        }
    }
}