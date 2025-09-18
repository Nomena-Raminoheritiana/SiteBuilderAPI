<?php 
namespace App\ApiResource\Controller\Template;


use App\Repository\TemplateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class GetTemplatesByParentIdController extends AbstractController {

    public function __invoke(
        string $parentId,
        TemplateRepository $templateRepository
    ): array
    {
        try {
            return $templateRepository->findByUserAndParentOrId($parentId);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException('UUID not valid.');
        }
    }
}