<?php
namespace App\ApiResource\Controller\Image;
// src/Controller/ImageDownloadController.php

use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Storage\StorageInterface;

class ImageDownloadController extends AbstractController
{
    public function __construct(private readonly StorageInterface $storage) {}
    public function __invoke(Image $image): BinaryFileResponse
    {
        $filePath = $this->storage->resolvePath($image, 'file');

        if (!$filePath || !file_exists($filePath)) {
            throw new NotFoundHttpException('Fichier introuvable.');
        }

        return new BinaryFileResponse($filePath);
    }
}
