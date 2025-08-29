<?php
namespace App\ApiResource\Controller\Model;

use App\Entity\Image;
use App\Entity\Model;
use App\Repository\ModelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsController]
class UpsertModelLogoController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ModelRepository $modelRepository,
        private UrlGeneratorInterface $urlGenerator,
        private LoggerInterface $logger
    ) {}

    public function __invoke(Request $request, Model $model): Image
    {
        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->files->get('file');

        if (!$uploadedFile) {
            throw new \InvalidArgumentException('No file uploaded.');
        }

        $getLogoUrl = fn(Model $m) => $this->urlGenerator->generate(
            'api_image_download',
            ['id' => $m->getLogo()?->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
   
        $oldLogoUrl = $model->getLogo() ? $getLogoUrl($model) : null;
        $this->logger->info('voici le oldLogUrl'. $oldLogoUrl);

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

        $newLogoUrl = $getLogoUrl($model);
        if($oldLogoUrl) {
            $children = $this->modelRepository->findChildrenOrOrphans($model);
            function replaceUrls(array $props, string $oldUrl, string $newUrl): array {
                foreach ($props as $key => $value) {
                    if (is_string($value)) {
                        $props[$key] = str_replace($oldUrl, $newUrl, $value);
                    } elseif (is_array($value)) {
                        $props[$key] = replaceUrls($value, $oldUrl, $newUrl);
                    }
                }
                return $props;
            }
            foreach ($children as $child) {
                $props = $child->getProps(); 
                $newProps = replaceUrls($props, $oldLogoUrl, $newLogoUrl);
                if ($newProps !== $props) {
                    $child->setProps($newProps);
                    $this->em->persist($child);       
                }

            }
              $this->em->flush();
        }

        return $image;
    }
}