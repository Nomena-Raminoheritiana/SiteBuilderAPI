<?php
namespace App\ApiResource\Controller\Model;

use App\Entity\Image;
use App\Entity\Model;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UpsertModelLogoController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function __invoke(Request $request, Model $model): Image
    {
        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->files->get('file');

        if (!$uploadedFile) {
            throw new \InvalidArgumentException('No file uploaded.');
        }

        // Supprimer l'ancien logo s'il existe
        if ($oldLogo = $model->getLogo()) {
            $this->em->remove($oldLogo);
        }

        // Créer et associer une nouvelle image
        $image = new Image();
        $image->setFile($uploadedFile);
        $image->setModel($model);

        $model->setLogo($image);

        $this->em->persist($image);
        $this->em->persist($model);
        $this->em->flush();

        return $image;
    }
}