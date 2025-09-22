<?php
namespace App\ApiResource\Controller\Model;

use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\Model;
use App\Repository\StatusRepository;
use App\Repository\TemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateModelController extends AbstractController {
    public function __construct(
        private StatusRepository $statusRepository,
        private TemplateRepository $templateRepository,
        private EntityManagerInterface $em,
        private Security $security,
        private ValidatorInterface $validator, 
    ) {}

    public function __invoke(Model $model): Model
    {
        $user = $this->security->getUser();
        $status = $this->statusRepository->findOneBy(['code' => 'draft']);

        if ($user === null) {
            throw new \RuntimeException('No authenticated user found');
        }

        if($model->getTemplate()) {
            $template = $model->getTemplate();

            if (!$template) {
                throw new \RuntimeException('Template not found.');
            }

            foreach ($template->getChildren() as $childTemplate) {
                $childModel = new Model();
                $childModel->setUser($user);
                $childModel->setParent($model);

                $childModel->setName($childTemplate->getName());
                $childModel->setProps($childTemplate->getProps());
                $childModel->setCategory($childTemplate->getCategory());
                $childModel->setUrl($childTemplate->getUrl());
                $childModel->setSeo($model->getSeo());
                $childModel->setStatus($status);
                $childModel->setTemplate($childTemplate);

                $this->em->persist($childModel);
            }

            $model->setProps($template->getProps());
            $model->setUrl($model->getUrl() ?? $template->getUrl());
        }

        // Lier le modèle à l'utilisateur connecté
        $model->setUser($user);
        $model->setStatus($status);

        $errors = $this->validator->validate($model);
        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $this->em->persist($model);
        $this->em->flush();

        return $model;
    }
}
