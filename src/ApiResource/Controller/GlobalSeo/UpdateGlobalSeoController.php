<?php

namespace App\ApiResource\Controller\GlobalSeo;

use ApiPlatform\Metadata\IriConverterInterface;
use App\ApiResource\Dto\Input\GlobalSeo\GlobalSeoUpdateInput;
use App\Entity\GlobalSeo;
use App\Entity\Model;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class UpdateGlobalSeoController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private IriConverterInterface $iriConverter,
        private NormalizerInterface $normalizer
    ) {}

    public function __invoke(GlobalSeoUpdateInput $input): GlobalSeo
    {
        // Retrieve the GlobalSeo entity linked to the provided model
        try {
            /** @var Model $model */
            $model = $this->iriConverter->getResourceFromIri($input->model);
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Invalid model URI in "modelId".');
        }

        $globalSeo = $model->getGlobalSeo();
        if (!$globalSeo) {
            throw new BadRequestHttpException('No GlobalSeo is linked to the specified model.');
        }

        // Update GlobalSeo fields if provided
        if ($input->formValue) {
            $globalSeo->setFormValue($input->formValue);
        }

        if ($input->metadata) {
            $globalSeo->setMetadata($input->metadata);
        }

        // Update Model fields if provided
        if ($input->modelName) {
            $model->setName($input->modelName);
        }

        // ✅ Update category if provided
        if (!empty($input->category)) {
            try {
                /** @var Category $category */
                $category = $this->iriConverter->getResourceFromIri($input->category);
                $model->setCategory($category);
            } catch (\Exception $e) {
                throw new BadRequestHttpException('Invalid category IRI.');
            }
        }

        if ($input->seo) {
            $seoArray = $this->normalizer->normalize($input->seo, null, ['groups' => ['GlobalSeo:write']]);
            $seo = array_merge($model->getSeo() ?? [], $seoArray);
            $model->setSeo($seo);
        }

        $this->em->flush();

        return $globalSeo;
    }
}