<?php
// src/Controller/Api/ImageController.php
namespace App\Controller\Api;

use App\Entity\Image;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ImageController extends AbstractController
{
    #[Route('/api/images/upload', name: 'api_images_upload', methods: ['POST'])]
    public function upload(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($image);
            $entityManager->flush();

            return new JsonResponse([
                'id' => $image->getId(),
                'idFromFront' => $image->getIdFromFront(),
                'url' => $this->generateUrl('api_images_get', ['id' => $image->getId()])
            ], JsonResponse::HTTP_CREATED);
        }

        return new JsonResponse([
            'errors' => (string) $form->getErrors(true, false)
        ], JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/api/images/{id}', name: 'api_images_get', methods: ['GET'])]
    public function getImage(Image $image): JsonResponse
    {
        $url = $this->generateUrl('api_images_get', ['id' => $image->getId()]);
        return new JsonResponse(['url' => $url]);
    }
}
